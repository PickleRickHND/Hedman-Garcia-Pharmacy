-- ============================================
-- Migration: Add codigo_expires column to Usuarios table
-- Date: 2025-11-29
-- Description: Adds expiration time for password recovery codes
-- ============================================

-- Add codigo_expires column if it doesn't exist
ALTER TABLE Usuarios
ADD COLUMN IF NOT EXISTS codigo_expires TIMESTAMP NULL DEFAULT NULL
AFTER codigo;

-- Update the codigo column to allow longer hashed values
ALTER TABLE Usuarios
MODIFY COLUMN codigo VARCHAR(255) DEFAULT NULL;

-- Add index for faster queries on codigo_expires
CREATE INDEX IF NOT EXISTS idx_usuarios_codigo_expires ON Usuarios(codigo_expires);

-- Clear any existing codes (optional - recommended for security after migration)
-- UPDATE Usuarios SET codigo = NULL, codigo_expires = NULL WHERE codigo IS NOT NULL;
