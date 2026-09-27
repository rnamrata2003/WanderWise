<?php

include "db.php";

$user_id = 1;


/*
    Get user's trips and match them
    with destination characteristics
*/

$sql = "SELECT
            trips.rating,
            destinations.nature,
            destinations.mountains,
            destinations.beach,
            destinations.adventure,
            destinations.peace,
            destinations.culture
        FROM trips
        JOIN destinations
        ON trips.destination = destinations.name
        WHERE trips.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();


/* Preference totals */

$nature = 0;
$mountains = 0;
$beach = 0;
$adventure = 0;
$peace = 0;
$culture = 0;

$total_rating = 0;


/* Calculate preferences */

while ($row = $result->fetch_assoc()) {

    $rating = $row['rating'];

    $nature += $row['nature'] * $rating;
    $mountains += $row['mountains'] * $rating;
    $beach += $row['beach'] * $rating;
    $adventure += $row['adventure'] * $rating;
    $peace += $row['peace'] * $rating;
    $culture += $row['culture'] * $rating;

    $total_rating += $rating;
}


/* Avoid division by zero */

if ($total_rating > 0) {

    $nature = round($nature / $total_rating, 2);
    $mountains = round($mountains / $total_rating, 2);
    $beach = round($beach / $total_rating, 2);
    $adventure = round($adventure / $total_rating, 2);
    $peace = round($peace / $total_rating, 2);
    $culture = round($culture / $total_rating, 2);
}


/* Preference data */

$preferences = [

    [
        "name" => "Nature",
        "icon" => "🌿",
        "value" => $nature
    ],

    [
        "name" => "Mountains",
        "icon" => "⛰️",
        "value" => $mountains
    ],

    [
        "name" => "Beach",
        "icon" => "🏖️",
        "value" => $beach
    ],

    [
        "name" => "Adventure",
        "icon" => "🧗",
        "value" => $adventure
    ],

    [
        "name" => "Peace",
        "icon" => "🧘",
        "value" => $peace
    ],

    [
        "name" => "Culture",
        "icon" => "🏛️",
        "value" => $culture
    ]

];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Travel Preferences - WanderWise</title>


<style>

/* ==========================================
   GLOBAL
========================================== */

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


/* ==========================================
   NAVBAR
========================================== */

.navbar {

    background:
        rgba(255,255,255,0.94);

    border-bottom:
        1px solid #dceef5;

    position: sticky;

    top: 0;

    z-index: 10;

    backdrop-filter:
        blur(10px);
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


/* ==========================================
   MAIN
========================================== */

.container {

    max-width: 900px;

    margin: auto;

    padding: 50px 25px 70px;
}


.header {

    margin-bottom: 30px;
}


.header h1 {

    font-size: 35px;

    color: #173b55;

    margin-bottom: 8px;
}


.header p {

    color: #718796;

    font-size: 15px;

    line-height: 1.6;
}


/* ==========================================
   PREFERENCE LIST
========================================== */

.preference-list {

    display: flex;

    flex-direction: column;

    gap: 13px;
}


.preference {

    background: white;

    border: 1px solid #dceef5;

    border-radius: 14px;

    padding: 18px 20px;

    display: grid;

    grid-template-columns:
        190px 1fr 80px;

    align-items: center;

    gap: 20px;

    box-shadow:
        0 5px 15px
        rgba(24,109,138,0.04);

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;
}


.preference:hover {

    transform: translateX(5px);

    border-color: #9ed9e7;

    box-shadow:
        0 10px 24px
        rgba(24,109,138,0.10);
}


/* ==========================================
   FEATURE NAME
========================================== */

.feature {

    display: flex;

    align-items: center;

    gap: 11px;

    font-weight: 700;

    color: #35586d;
}


.feature-icon {

    width: 40px;

    height: 40px;

    border-radius: 11px;

    background: #e8f8fc;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 21px;

    transition:
        transform 0.25s ease,
        background 0.25s ease;
}


.preference:hover .feature-icon {

    transform: scale(1.08);

    background: #d9f3f9;
}


/* ==========================================
   PROGRESS BAR
========================================== */

.progress {

    height: 9px;

    background: #e7f1f5;

    border-radius: 20px;

    overflow: hidden;
}


.progress-bar {

    height: 100%;

    border-radius: 20px;

    background:
        linear-gradient(
            90deg,
            #52c9dc,
            #087ea4
        );

    transition:
        width 0.8s ease;
}


.preference:hover .progress-bar {

    background:
        linear-gradient(
            90deg,
            #39bdd3,
            #056c8c
        );
}


/* ==========================================
   SCORE
========================================== */

.score {

    text-align: right;

    font-weight: 800;

    color: #087ea4;

    font-size: 16px;
}


.score span {

    color: #8195a1;

    font-size: 12px;

    font-weight: 500;
}


/* ==========================================
   INFO
========================================== */

.info-box {

    margin-top: 25px;

    padding: 18px 20px;

    background: #eaf8fc;

    border-left:
        4px solid #1597b8;

    border-radius: 10px;

    color: #567383;

    font-size: 14px;

    line-height: 1.6;
}


/* ==========================================
   ACTIONS
========================================== */

.actions {

    margin-top: 28px;

    display: flex;

    gap: 12px;

    flex-wrap: wrap;
}


.button {

    display: inline-block;

    text-decoration: none;

    padding: 10px 17px;

    border-radius: 9px;

    font-size: 14px;

    font-weight: 700;

    transition:
        transform 0.25s ease,
        background 0.25s ease,
        box-shadow 0.25s ease;
}


.primary {

    background: #087ea4;

    color: white;

    box-shadow:
        0 6px 15px
        rgba(8,126,164,0.18);
}


.primary:hover {

    background: #066b8b;

    transform: translateY(-2px);

    box-shadow:
        0 9px 20px
        rgba(8,126,164,0.25);
}


.secondary {

    background: white;

    color: #087ea4;

    border: 1px solid #cfe7ef;
}


.secondary:hover {

    background: #edfaff;

    transform: translateY(-2px);
}


/* ==========================================
   FOOTER
========================================== */

.footer {

    text-align: center;

    margin-top: 45px;

    color: #8496a1;

    font-size: 13px;
}


/* ==========================================
   RESPONSIVE
========================================== */

@media (max-width: 700px) {

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
            35px 15px 50px;
    }


    .header h1 {

        font-size: 29px;
    }


    .preference {

        grid-template-columns: 1fr;

        gap: 12px;
    }


    .score {

        text-align: left;
    }

}

</style>

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

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


            <a
                href="preferences.php"
                class="active"
            >
                Preferences
            </a>


            <a href="recommend.php">
                AI Recommendations
            </a>

        </div>

    </div>

</nav>



<!-- ==========================================
     MAIN CONTENT
========================================== -->

<main class="container">


    <div class="header">

        <h1>
            Your Travel Preferences
        </h1>

        <p>
            Based on the destinations you have visited
            and the ratings you gave them, WanderWise
            has identified your travel preferences.
        </p>

    </div>



    <!-- PREFERENCE LIST -->

    <div class="preference-list">


        <?php foreach ($preferences as $preference): ?>


            <?php

            $percentage =
                round(
                    ($preference['value'] / 5) * 100
                );

            ?>


            <div class="preference">


                <!-- Feature -->

                <div class="feature">

                    <div class="feature-icon">

                        <?php

                        echo $preference['icon'];

                        ?>

                    </div>


                    <?php

                    echo htmlspecialchars(
                        $preference['name']
                    );

                    ?>

                </div>



                <!-- Progress -->

                <div class="progress">

                    <div
                        class="progress-bar"
                        style="
                            width:
                            <?php echo $percentage; ?>%;
                        "
                    ></div>

                </div>



                <!-- Score -->

                <div class="score">

                    <?php

                    echo $preference['value'];

                    ?>

                    <span>/ 5</span>

                </div>


            </div>


        <?php endforeach; ?>


    </div>



    <!-- INFORMATION -->

    <div class="info-box">

        💡 These preferences are calculated from
        your previous travel ratings. They help
        WanderWise understand what types of
        destinations you enjoy.

    </div>



    <!-- ACTIONS -->

    <div class="actions">


        <a
            href="my_trips.php"
            class="button secondary"
        >
            ← My Trips
        </a>


        <a
            href="recommend.php"
            class="button primary"
        >
            ✨ View AI Recommendations
        </a>


    </div>



    <div class="footer">

        WanderWise · Personalized Travel Intelligence

    </div>


</main>


</body>

</html>