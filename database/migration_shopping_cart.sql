-- ============================================
-- Shopping Cart Migration Script
-- ============================================
-- Purpose: Migrate from dynamic ShoppingCartUser_X tables
--          to unified Shopping_Cart table
-- Date: 2025-11-23
-- ============================================

USE FarmaciaHG;

-- Step 1: Create backup of existing shopping cart tables
-- Note: This script will identify all ShoppingCartUser_X tables
-- and migrate their data to the new Shopping_Cart table

-- ============================================
-- Migration Procedure
-- ============================================

DELIMITER //

CREATE PROCEDURE MigrateShoppingCartTables()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE table_name VARCHAR(255);
    DECLARE user_id_extracted INT;
    DECLARE sql_statement TEXT;

    -- Cursor to get all ShoppingCartUser tables
    DECLARE cart_cursor CURSOR FOR
        SELECT TABLE_NAME
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = 'FarmaciaHG'
        AND TABLE_NAME LIKE 'ShoppingCartUser_%';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Start migration
    SELECT 'Starting migration of shopping cart tables...' as status;

    OPEN cart_cursor;

    read_loop: LOOP
        FETCH cart_cursor INTO table_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Extract user ID from table name (ShoppingCartUser_123 -> 123)
        SET user_id_extracted = CAST(SUBSTRING(table_name, 17) AS UNSIGNED);

        -- Check if user exists
        IF EXISTS (SELECT 1 FROM Usuarios WHERE id = user_id_extracted) THEN
            -- Build dynamic SQL to migrate data
            SET @sql = CONCAT(
                'INSERT INTO Shopping_Cart (usuario_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) ',
                'SELECT ', user_id_extracted, ', ',
                'id_producto, nombre_producto, cantidad_producto, precio_producto, subtotal ',
                'FROM ', table_name,
                ' ON DUPLICATE KEY UPDATE ',
                'cantidad = VALUES(cantidad), ',
                'precio_unitario = VALUES(precio_unitario), ',
                'subtotal = VALUES(subtotal)'
            );

            -- Execute migration
            PREPARE stmt FROM @sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;

            SELECT CONCAT('Migrated data from ', table_name, ' for user ', user_id_extracted) as status;
        ELSE
            SELECT CONCAT('Skipped ', table_name, ' - user ', user_id_extracted, ' does not exist') as warning;
        END IF;
    END LOOP;

    CLOSE cart_cursor;

    SELECT 'Migration completed!' as status;
END//

DELIMITER ;

-- ============================================
-- Procedure to drop old shopping cart tables
-- ============================================

DELIMITER //

CREATE PROCEDURE DropOldShoppingCartTables()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE table_name VARCHAR(255);

    -- Cursor to get all ShoppingCartUser tables
    DECLARE cart_cursor CURSOR FOR
        SELECT TABLE_NAME
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = 'FarmaciaHG'
        AND TABLE_NAME LIKE 'ShoppingCartUser_%';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SELECT 'Starting cleanup of old shopping cart tables...' as status;

    OPEN cart_cursor;

    read_loop: LOOP
        FETCH cart_cursor INTO table_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Drop the table
        SET @sql = CONCAT('DROP TABLE IF EXISTS ', table_name);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        SELECT CONCAT('Dropped table: ', table_name) as status;
    END LOOP;

    CLOSE cart_cursor;

    SELECT 'Cleanup completed!' as status;
END//

DELIMITER ;

-- ============================================
-- Manual Execution Steps
-- ============================================
-- Run these commands in order:

-- Step 1: Make sure Shopping_Cart table exists
-- (Run schema.sql first if not created)

-- Step 2: Run migration procedure
-- CALL MigrateShoppingCartTables();

-- Step 3: Verify migration
-- SELECT usuario_id, COUNT(*) as items_count, SUM(subtotal) as total
-- FROM Shopping_Cart
-- GROUP BY usuario_id;

-- Step 4: After verification, drop old tables
-- CALL DropOldShoppingCartTables();

-- Step 5: Clean up procedures (optional)
-- DROP PROCEDURE IF EXISTS MigrateShoppingCartTables;
-- DROP PROCEDURE IF EXISTS DropOldShoppingCartTables;

-- ============================================
-- Rollback Plan (in case of issues)
-- ============================================
-- If something goes wrong, you can restore from backup:
-- 1. Stop the application
-- 2. Restore database from backup
-- 3. Investigate the issue
-- 4. Fix and retry migration
-- ============================================
