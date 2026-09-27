<?php
include "db.php";

$user_id = 1;

$sql = "SELECT * FROM trips
        WHERE user_id = ?
        ORDER BY travel_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Trips - WanderWise</title>


    <style>

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
                    #f1fbfe,
                    #f8fcff
                );

            color: #173b55;

            min-height: 100vh;
        }


        /* =========================
           NAVBAR
        ========================= */

        .navbar {

            background: rgba(255,255,255,0.94);

            border-bottom: 1px solid #dceef5;

            position: sticky;

            top: 0;

            z-index: 10;

            backdrop-filter: blur(10px);
        }


        .nav-container {

            max-width: 1100px;

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

            transition: 0.25s;
        }


        .logo:hover {

            color: #055f7c;

            transform: translateY(-1px);
        }


        .nav-links {

            display: flex;

            gap: 8px;
        }


        .nav-links a {

            text-decoration: none;

            color: #60798a;

            padding: 8px 13px;

            border-radius: 9px;

            font-size: 14px;

            font-weight: 600;

            transition: 0.25s;
        }


        .nav-links a:hover {

            color: #087ea4;

            background: #e6f7fc;

            transform: translateY(-1px);
        }


        .nav-links .active {

            color: #087ea4;

            background: #e3f6fb;
        }


        /* =========================
           MAIN
        ========================= */

        .container {

            max-width: 1050px;

            margin: auto;

            padding: 45px 25px 70px;
        }


        .page-header {

            margin-bottom: 30px;
        }


        .page-header h1 {

            font-size: 34px;

            color: #173b55;

            margin-bottom: 7px;
        }


        .page-header p {

            color: #718796;

            font-size: 15px;
        }


        /* =========================
           TRIP LIST
        ========================= */

        .trip-list {

            display: flex;

            flex-direction: column;

            gap: 16px;
        }


        .trip {

            background: white;

            border: 1px solid #dceef5;

            border-radius: 16px;

            padding: 22px 24px;

            box-shadow:
                0 6px 18px
                rgba(24, 109, 138, 0.05);

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }


        .trip:hover {

            transform: translateY(-4px);

            border-color: #a9dce9;

            box-shadow:
                0 12px 28px
                rgba(24, 109, 138, 0.11);
        }


        .trip-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            margin-bottom: 17px;
        }


        .destination {

            font-size: 22px;

            font-weight: 750;

            color: #087ea4;

            transition: 0.25s;
        }


        .trip:hover .destination {

            color: #056b8a;
        }


        .rating {

            background: #e6f8f2;

            color: #137a62;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 700;

            white-space: nowrap;
        }


        .trip-info {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 10px;

            margin-bottom: 18px;
        }


        .info {

            color: #607887;

            font-size: 14px;
        }


        .info strong {

            color: #35586d;
        }


        .experience {

            border-top: 1px solid #edf4f7;

            padding-top: 16px;

            color: #607887;

            font-size: 14px;

            line-height: 1.7;
        }


        .experience-label {

            display: block;

            color: #35586d;

            font-weight: 700;

            margin-bottom: 5px;
        }


        /* =========================
           EMPTY STATE
        ========================= */

        .empty {

            background: white;

            border: 1px dashed #b9dce7;

            border-radius: 16px;

            padding: 40px;

            text-align: center;

            color: #718796;
        }


        /* =========================
           BUTTON
        ========================= */

        .actions {

            margin-top: 30px;
        }


        .add-button {

            display: inline-block;

            text-decoration: none;

            background: #087ea4;

            color: white;

            padding: 11px 18px;

            border-radius: 10px;

            font-size: 14px;

            font-weight: 700;

            box-shadow:
                0 6px 15px
                rgba(8,126,164,0.18);

            transition:
                transform 0.25s ease,
                background 0.25s ease,
                box-shadow 0.25s ease;
        }


        .add-button:hover {

            background: #066b8b;

            transform: translateY(-2px);

            box-shadow:
                0 9px 20px
                rgba(8,126,164,0.25);
        }


        /* =========================
           FOOTER
        ========================= */

        .footer {

            text-align: center;

            color: #8496a1;

            font-size: 13px;

            margin-top: 45px;
        }


        /* =========================
           RESPONSIVE
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

                padding: 30px 15px 50px;
            }


            .page-header h1 {

                font-size: 29px;
            }


            .trip-header {

                align-items: flex-start;

                flex-direction: column;
            }


            .trip-info {

                grid-template-columns: 1fr;
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

        <a href="recommend.php" class="logo">
            🌊 WanderWise
        </a>


        <div class="nav-links">

            <a
                href="my_trips.php"
                class="active"
            >
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
     MAIN CONTENT
========================= -->

<main class="container">


    <div class="page-header">

        <h1>
            My Travel History
        </h1>

        <p>
            Your personal collection of travel experiences.
        </p>

    </div>



    <?php

    if ($result->num_rows > 0) {

        echo '<div class="trip-list">';


        while ($trip = $result->fetch_assoc()) {

            ?>

            <div class="trip">


                <div class="trip-header">

                    <div class="destination">

                        🌍
                        <?php

                        echo htmlspecialchars(
                            $trip['destination']
                        );

                        ?>

                    </div>


                    <div class="rating">

                        ⭐
                        <?php

                        echo htmlspecialchars(
                            $trip['rating']
                        );

                        ?>/5

                    </div>

                </div>



                <div class="trip-info">


                    <div class="info">

                        <strong>Date:</strong>

                        <?php

                        echo htmlspecialchars(
                            $trip['travel_date']
                        );

                        ?>

                    </div>


                    <div class="info">

                        <strong>Budget:</strong>

                        ₹<?php

                        echo htmlspecialchars(
                            $trip['budget']
                        );

                        ?>

                    </div>


                </div>



                <div class="experience">

                    <span class="experience-label">

                        Your Experience

                    </span>


                    <?php

                    echo nl2br(
                        htmlspecialchars(
                            $trip['experience']
                        )
                    );

                    ?>

                </div>


            </div>

            <?php

        }


        echo '</div>';

    } else {

        ?>

        <div class="empty">

            <p>
                You haven't added any trips yet.
            </p>

        </div>

        <?php

    }

    ?>



    <div class="actions">

        <a
            href="add_trip.php"
            class="add-button"
        >
            + Add Another Trip
        </a>

    </div>



    <div class="footer">

        WanderWise · Your Travel Diary

    </div>


</main>


</body>

</html>