-- Update the appointment table to support our new booking process

-- Add columns for storing user/pet information
ALTER TABLE appointment 
ADD COLUMN IF NOT EXISTS owner_name VARCHAR(100),
ADD COLUMN IF NOT EXISTS contact_number VARCHAR(20),
ADD COLUMN IF NOT EXISTS email VARCHAR(100),
ADD COLUMN IF NOT EXISTS pet_name VARCHAR(100),
ADD COLUMN IF NOT EXISTS pet_type VARCHAR(50),
ADD COLUMN IF NOT EXISTS preferred_date DATE,
ADD COLUMN IF NOT EXISTS preferred_time TIME,
ADD COLUMN IF NOT EXISTS appointment_type VARCHAR(20),
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'pending';

-- Add indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_appointment_client ON appointment(client_code);
CREATE INDEX IF NOT EXISTS idx_appointment_pet ON appointment(pet_code);
CREATE INDEX IF NOT EXISTS idx_appointment_date_time ON appointment(preferred_date, preferred_time);

-- Update existing records if needed
UPDATE appointment 
SET status = 'pending' 
WHERE status IS NULL;

-- Comment this part if you don't want to make these columns required
ALTER TABLE appointment 
ALTER COLUMN client_code SET NOT NULL,
ALTER COLUMN pet_code SET NOT NULL,
ALTER COLUMN preferred_date SET NOT NULL,
ALTER COLUMN preferred_time SET NOT NULL; 