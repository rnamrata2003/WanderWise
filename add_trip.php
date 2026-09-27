<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $destination = $_POST["destination"];
    $travel_date = $_POST["travel_date"];
    $budget = $_POST["budget"];
    $rating = $_POST["rating"];
    $experience = $_POST["experience"];

    $user_id = 1; // temporary user

    $sql = "INSERT INTO trips
            (user_id, destination, travel_date, budget, rating, experience)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "issdis",
        $user_id,
        $destination,
        $travel_date,
        $budget,
        $rating,
        $experience
    );

    if ($stmt->execute()) {
        $message = "Trip added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Trip - WanderWise</title>


    <style>

        /* =========================
           BASIC RESET
        ========================= */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #f0faff,
                    #f9fdff
                );

            color: #173b55;

            min-height: 100vh;
        }


        /* =========================
           NAVBAR
        ========================= */

        .navbar {

            background: white;

            border-bottom:
                1px solid #dceef5;

            position: sticky;

            top: 0;

            z-index: 10;
        }


        .nav-container {

            max-width: 1050px;

            margin: auto;

            padding: 17px 25px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }


        .logo {

            text-decoration: none;

            color: #087ea4;

            font-size: 23px;

            font-weight: 800;

            transition: 0.2s;
        }


        .logo:hover {

            color: #056b8a;
        }


        .nav-links {

            display: flex;

            gap: 8px;
        }


        .nav-links a {

            text-decoration: none;

            color: #60798a;

            padding: 8px 13px;

            border-radius: 8px;

            font-size: 14px;

            font-weight: 600;

            transition: 0.2s;
        }


        .nav-links a:hover {

            color: #087ea4;

            background: #e6f7fc;
        }


        .nav-links .active {

            color: #087ea4;

            background: #e3f6fb;
        }


        /* =========================
           MAIN
        ========================= */

        .container {

            max-width: 700px;

            margin: auto;

            padding: 45px 25px 60px;
        }


        .page-header {

            margin-bottom: 25px;
        }


        .page-header h1 {

            font-size: 34px;

            margin-bottom: 7px;

            color: #173b55;
        }


        .page-header p {

            color: #718796;

            font-size: 15px;
        }


        /* =========================
           FORM
        ========================= */

        .form-container {

            background: white;

            border:
                1px solid #dceef5;

            border-radius: 16px;

            padding: 30px;

            box-shadow:
                0 7px 20px
                rgba(24,109,138,0.06);
        }


        .form-group {

            margin-bottom: 20px;
        }


        label {

            display: block;

            margin-bottom: 7px;

            color: #35586d;

            font-size: 14px;

            font-weight: 700;
        }


        input,
        textarea {

            width: 100%;

            padding: 11px 13px;

            border:
                1px solid #cfe4eb;

            border-radius: 9px;

            background: #fbfeff;

            color: #294c60;

            font-family: inherit;

            font-size: 14px;

            outline: none;

            transition:
                border-color 0.2s,
                box-shadow 0.2s,
                background 0.2s;
        }


        input:hover,
        textarea:hover {

            border-color: #9bcfdd;

            background: white;
        }


        input:focus,
        textarea:focus {

            border-color: #087ea4;

            background: white;

            box-shadow:
                0 0 0 3px
                rgba(8,126,164,0.10);
        }


        textarea {

            min-height: 130px;

            resize: vertical;

            line-height: 1.6;
        }


        /* =========================
           MESSAGE
        ========================= */

        .message {

            padding: 11px 14px;

            border-radius: 9px;

            margin-bottom: 22px;

            font-size: 14px;

            background: #e8f8f2;

            color: #14755f;

            border:
                1px solid #c8ebdf;
        }


        /* =========================
           BUTTON
        ========================= */

        .submit-button {

            width: 100%;

            border: none;

            background: #087ea4;

            color: white;

            padding: 12px;

            border-radius: 9px;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            transition:
                background 0.2s,
                transform 0.2s,
                box-shadow 0.2s;
        }


        .submit-button:hover {

            background: #066b8b;

            transform: translateY(-2px);

            box-shadow:
                0 7px 16px
                rgba(8,126,164,0.20);
        }


        .submit-button:active {

            transform: translateY(0);
        }


        /* =========================
           BACK LINK
        ========================= */

        .back-link {

            display: inline-block;

            margin-top: 20px;

            text-decoration: none;

            color: #087ea4;

            font-size: 14px;

            font-weight: 600;

            transition: 0.2s;
        }


        .back-link:hover {

            color: #055f7c;

            transform: translateX(-2px);
        }


        /* =========================
           FOOTER
        ========================= */

        .footer {

            text-align: center;

            margin-top: 35px;

            color: #8496a1;

            font-size: 13px;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 650px) {

            .nav-container {

                flex-direction: column;

                gap: 12px;
            }


            .nav-links {

                flex-wrap: wrap;

                justify-content: center;
            }


            .container {

                padding:
                    30px 15px 50px;
            }


            .page-header h1 {

                font-size: 29px;
            }


            .form-container {

                padding: 22px;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar">

    <div class="nav-container">


        <a
            href="recommend.php"
            class="logo"
        >
            🌊 WanderWise
        </a>


        <div class="nav-links">

            <a href="my_trips.php">
                My Trips
            </a>


            <a href="preferences.php">
                Preferences
            </a>


            <a href="recommend.php">
                AI Recommendations
            </a>

        </div>

    </div>

</nav>



<!-- =========================
     MAIN
========================= -->

<main class="container">


    <div class="page-header">

        <h1>
            Add Your Trip
        </h1>

        <p>
            Record your travel experience and help
            WanderWise understand your preferences.
        </p>

    </div>



    <div class="form-container">


        <?php

        if ($message != "") {

            echo "<div class='message'>"
                 . htmlspecialchars($message)
                 . "</div>";
        }

        ?>


        <form method="POST">


            <!-- Destination -->

            <div class="form-group">

                <label for="destination">
                    Destination
                </label>

                <input
                    type="text"
                    id="destination"
                    name="destination"
                    placeholder="e.g. Sikkim"
                    required
                >

            </div>



            <!-- Date -->

            <div class="form-group">

                <label for="travel_date">
                    Travel Date
                </label>

                <input
                    type="date"
                    id="travel_date"
                    name="travel_date"
                    required
                >

            </div>



            <!-- Budget -->

            <div class="form-group">

                <label for="budget">
                    Budget
                </label>

                <input
                    type="number"
                    id="budget"
                    name="budget"
                    placeholder="Enter your budget"
                    min="0"
                    required
                >

            </div>



            <!-- Rating -->

            <div class="form-group">

                <label for="rating">
                    Rating
                </label>

                <input
                    type="number"
                    id="rating"
                    name="rating"
                    min="1"
                    max="5"
                    placeholder="1 - 5"
                    required
                >

            </div>



            <!-- Experience -->

            <div class="form-group">

                <label for="experience">
                    How was your experience?
                </label>

                <textarea
                    id="experience"
                    name="experience"
                    placeholder="Describe your travel experience..."
                    required
                ></textarea>

            </div>



            <!-- Submit -->

            <button
                type="submit"
                class="submit-button"
            >
                Save Trip
            </button>


        </form>


        <a
            href="my_trips.php"
            class="back-link"
        >
            ← Back to My Trips
        </a>


    </div>



    <div class="footer">

        WanderWise · Your Travel Diary

    </div>


</main>


</body>

</html>