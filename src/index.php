<?php

// Database configuration
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('POSTGRES_DB');
$username = getenv('POSTGRES_USER');
$password = getenv('POSTGRES_PASSWORD');

try {
    // Connect to PostgreSQL
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// Handle booking form submission
$booking_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customer_name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $destination_id = $_POST['destination_id'];
    $travel_date = $_POST['travel_date'];
    $guests = $_POST['guests'];

    try {

        $stmt = $pdo->prepare("
            INSERT INTO bookings
            (customer_name, email, destination_id, travel_date, guests)
            VALUES
            (:customer_name, :email, :destination_id, :travel_date, :guests)
        ");

        $stmt->execute([
            ':customer_name' => $customer_name,
            ':email' => $email,
            ':destination_id' => $destination_id,
            ':travel_date' => $travel_date,
            ':guests' => $guests
        ]);

        $booking_message = "Your booking has been submitted successfully!";

    } catch (PDOException $e) {

        $booking_message = "Booking failed. Please try again.";

    }
}


// Fetch destinations
try {

    $stmt = $pdo->query("
        SELECT *
        FROM destinations
        ORDER BY id
    ");

    $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $destinations = [];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Wanderly | Explore the Kashmir</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f7f8;
            color: #222;
        }

        nav {
            background: white;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            color: #176b87;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav a {
            text-decoration: none;
            color: #333;
        }

        .hero {
            min-height: 500px;
            display: flex;
            align-items: center;
            padding: 70px 8%;
            background:
                linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                url("https://images.unsplash.com/photo-1500530855697-b586d89ba3ee")
                center/cover;
            color: white;
        }

        .hero-content {
            max-width: 650px;
        }

        .hero h1 {
            font-size: 52px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 13px 25px;
            background: #176b87;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        .destinations {
            padding: 60px 8%;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card h3 {
            margin-bottom: 8px;
        }

        .card p {
            color: #666;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .price {
            font-weight: bold;
            color: #176b87;
        }

        .booking {
            background: white;
            padding: 60px 8%;
        }

        .booking-form {
            max-width: 600px;
            margin: auto;
        }

        .booking-form input,
        .booking-form select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .booking-form label {
            font-weight: bold;
        }

        .message {
            max-width: 600px;
            margin: 0 auto 25px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 5px;
            text-align: center;
        }

        footer {
            background: #12343b;
            color: white;
            text-align: center;
            padding: 25px;
        }

        @media (max-width: 768px) {

            .cards {
                grid-template-columns: 1fr;
            }

            nav ul {
                display: none;
            }

            .hero h1 {
                font-size: 38px;
            }

        }

    </style>

</head>

<body>

<nav>

    <div class="logo">
        Wanderly
    </div>

    <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#destinations">Destinations</a></li>
        <li><a href="#booking">Book Now</a></li>
    </ul>

</nav>


<section class="hero">

    <div class="hero-content">

        <h1>
            Explore. Discover. Wander. Enjoy The Beauty of Kashmir.
        </h1>

        <p>
            Discover beautiful destinations, plan your next adventure
            and create unforgettable travel experiences.
        </p>

        <a href="#destinations" class="btn">
            Explore Destinations
        </a>

    </div>

</section>


<section class="destinations" id="destinations">

    <div class="section-title">

        <h2>
            Popular Destinations
        </h2>

        <p>
            Explore some of the world's most beautiful places.
        </p>

    </div>


    <div class="cards">

        <?php if (empty($destinations)): ?>

            <p>
                No destinations available.
            </p>

        <?php else: ?>

            <?php foreach ($destinations as $destination): ?>

                <div class="card">

                    <img
                        src="<?= htmlspecialchars($destination['image_url']) ?>"
                        alt="<?= htmlspecialchars($destination['name']) ?>"
                    >

                    <div class="card-content">

                        <h3>
                            <?= htmlspecialchars($destination['name']) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($destination['location']) ?>
                        </p>

                        <p>
                            <?= htmlspecialchars($destination['description']) ?>
                        </p>

                        <div class="price">
                            ₹<?= number_format($destination['price'], 2) ?>
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</section>


<section class="booking" id="booking">

    <div class="section-title">

        <h2>
            Book Your Trip
        </h2>

        <p>
            Fill in the details below to plan your next adventure.
        </p>

    </div>


    <?php if ($booking_message): ?>

        <div class="message">
            <?= htmlspecialchars($booking_message) ?>
        </div>

    <?php endif; ?>


    <form
        method="POST"
        class="booking-form"
    >

        <label>
            Full Name
        </label>

        <input
            type="text"
            name="customer_name"
            placeholder="Enter your name"
            required
        >


        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >


        <label>
            Destination
        </label>

        <select
            name="destination_id"
            required
        >

            <option value="">
                Select a destination
            </option>

            <?php foreach ($destinations as $destination): ?>

                <option value="<?= $destination['id'] ?>">
                    <?= htmlspecialchars($destination['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>


        <label>
            Travel Date
        </label>

        <input
            type="date"
            name="travel_date"
            required
        >


        <label>
            Number of Guests
        </label>

        <input
            type="number"
            name="guests"
            min="1"
            max="20"
            required
        >


        <button
            type="submit"
            class="btn"
        >
            Confirm Booking
        </button>

    </form>

</section>


<footer>

    <p>
        &copy; 2026 Wanderly. Explore the world.
    </p>

</footer>

</body>

</html>
