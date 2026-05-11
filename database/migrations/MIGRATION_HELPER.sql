-- ============================================
-- SUPER ADMIN MULTI-BUSINESS MIGRATION HELPER
-- ============================================
-- This file contains SQL commands to help migrate existing data
-- to the new multi-business structure
-- 
-- IMPORTANT: Review and modify these queries based on your actual data
-- Run these AFTER running the Laravel migrations
-- ============================================

-- Step 1: Create a default business for existing data
-- Modify the business details as needed
INSERT INTO businesses (name, slug, business_type, description, status, created_at, updated_at)
VALUES (
    'Default Palm Oil Business',
    'default-palm-oil-business',
    'Palm Oil',
    'Migrated from single-business system',
    'active',
    NOW(),
    NOW()
);

-- Get the ID of the business we just created (usually 1)
SET @default_business_id = LAST_INSERT_ID();

-- Step 2: Assign all existing users (except super_admin) to the default business
UPDATE users 
SET business_id = @default_business_id 
WHERE role != 'super_admin' AND business_id IS NULL;

-- Step 3: Assign all existing products to the default business
UPDATE products 
SET business_id = @default_business_id 
WHERE business_id IS NULL;

-- Step 4: Assign all existing sales to the default business
UPDATE sales 
SET business_id = @default_business_id 
WHERE business_id IS NULL;

-- Step 5: Assign all existing purchases to the default business
UPDATE purchases 
SET business_id = @default_business_id 
WHERE business_id IS NULL;

-- Step 6: Assign all existing expenses to the default business
UPDATE expenses 
SET business_id = @default_business_id 
WHERE business_id IS NULL;

-- Step 7: Assign all existing product assignments to the default business
UPDATE product_assignments 
SET business_id = @default_business_id 
WHERE business_id IS NULL;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if all users have business_id (except super_admin)
SELECT 
    role,
    COUNT(*) as total,
    SUM(CASE WHEN business_id IS NULL THEN 1 ELSE 0 END) as without_business
FROM users
GROUP BY role;

-- Check if all products have business_id
SELECT 
    COUNT(*) as total_products,
    SUM(CASE WHEN business_id IS NULL THEN 1 ELSE 0 END) as without_business
FROM products;

-- Check if all sales have business_id
SELECT 
    COUNT(*) as total_sales,
    SUM(CASE WHEN business_id IS NULL THEN 1 ELSE 0 END) as without_business
FROM sales;

-- View business summary
SELECT 
    b.id,
    b.name,
    b.status,
    COUNT(DISTINCT u.id) as total_users,
    COUNT(DISTINCT p.id) as total_products,
    COUNT(DISTINCT s.id) as total_sales
FROM businesses b
LEFT JOIN users u ON b.id = u.business_id
LEFT JOIN products p ON b.id = p.business_id
LEFT JOIN sales s ON b.id = s.business_id
GROUP BY b.id, b.name, b.status;

-- ============================================
-- OPTIONAL: Create additional sample businesses
-- ============================================

-- Example: Create Hizabrun Enterprises
INSERT INTO businesses (name, slug, business_type, description, phone, email, address, status, created_at, updated_at)
VALUES (
    'Hizabrun Enterprises',
    'hizabrun-enterprises',
    'Palm Oil',
    'Hizabrun palm oil retail business',
    '08012345678',
    'info@hizabrun.com',
    'Lagos, Nigeria',
    'active',
    NOW(),
    NOW()
);

-- Example: Create Smabgroup Medicine
INSERT INTO businesses (name, slug, business_type, description, phone, email, address, status, created_at, updated_at)
VALUES (
    'Smabgroup Medicine',
    'smabgroup-medicine',
    'Pharmaceutical',
    'Smabgroup pharmaceutical retail business',
    '08087654321',
    'info@smabgroupmed.com',
    'Abuja, Nigeria',
    'active',
    NOW(),
    NOW()
);

-- Example: Create Smabgroup Motor
INSERT INTO businesses (name, slug, business_type, description, phone, email, address, status, created_at, updated_at)
VALUES (
    'Smabgroup Motor',
    'smabgroup-motor',
    'Motor Parts',
    'Smabgroup motor parts retail business',
    '08098765432',
    'info@smabgroupmotor.com',
    'Kano, Nigeria',
    'active',
    NOW(),
    NOW()
);

-- ============================================
-- CLEANUP QUERIES (Use with caution)
-- ============================================

-- Remove test businesses (modify ID as needed)
-- DELETE FROM businesses WHERE id = 2;

-- Reset a business to inactive
-- UPDATE businesses SET status = 'inactive' WHERE id = 2;

-- ============================================
-- INDEX CREATION FOR PERFORMANCE
-- ============================================

-- Create indexes on business_id columns for better query performance
CREATE INDEX IF NOT EXISTS idx_users_business_id ON users(business_id);
CREATE INDEX IF NOT EXISTS idx_products_business_id ON products(business_id);
CREATE INDEX IF NOT EXISTS idx_sales_business_id ON sales(business_id);
CREATE INDEX IF NOT EXISTS idx_purchases_business_id ON purchases(business_id);
CREATE INDEX IF NOT EXISTS idx_expenses_business_id ON expenses(business_id);
CREATE INDEX IF NOT EXISTS idx_product_assignments_business_id ON product_assignments(business_id);

-- Composite indexes for common queries
CREATE INDEX IF NOT EXISTS idx_users_business_role ON users(business_id, role);
CREATE INDEX IF NOT EXISTS idx_products_business_stock ON products(business_id, current_stock);
CREATE INDEX IF NOT EXISTS idx_sales_business_date ON sales(business_id, sale_date);

-- ============================================
-- END OF MIGRATION HELPER
-- ============================================
