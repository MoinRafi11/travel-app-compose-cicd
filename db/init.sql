-- Create the travel application database


-- Create destinations table
CREATE TABLE IF NOT EXISTS destinations (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    image_url TEXT,
    category VARCHAR(100)
);

-- Insert sample travel destinations
INSERT INTO destinations
(name, location, description, price, image_url, category)
VALUES
(
    'Gulmarg',
    'Jammu & Kashmir',
    'Experience snow-covered mountains, skiing and breathtaking landscapes.',
    12000.00,
    'https://images.unsplash.com/photo-1519681393784-d120267933ba',
    'Mountain'
),
(
    'Goa',
    'India',
    'Relax on beautiful beaches and enjoy the vibrant coastal atmosphere.',
    15000.00,
    'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2',
    'Beach'
),
(
    'Dubai',
    'United Arab Emirates',
    'Explore modern architecture, luxury shopping and unforgettable desert adventures.',
    45000.00,
    'https://images.unsplash.com/photo-1512453979798-5ea266f8880c',
    'City'
);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id SERIAL PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    destination_id INTEGER REFERENCES destinations(id),
    travel_date DATE NOT NULL,
    guests INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Insert sample booking data
INSERT INTO bookings
(customer_name, email, destination_id, travel_date, guests)
VALUES
(
    'Demo User',
    'demo@example.com',
    1,
    '2026-10-15',
    2
),
(
    'Test Customer',
    'customer@example.com',
    2,
    '2026-11-05',
    3
);