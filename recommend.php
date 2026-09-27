<?php

include "db.php";

$user_id = 1;


/* ==========================================
   STEP 1: Calculate rating-based preferences
========================================== */

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

$nature = 0;
$mountains = 0;
$beach = 0;
$adventure = 0;
$peace = 0;
$culture = 0;

$total_rating = 0;

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

if ($total_rating > 0) {

    $nature /= $total_rating;
    $mountains /= $total_rating;
    $beach /= $total_rating;
    $adventure /= $total_rating;
    $peace /= $total_rating;
    $culture /= $total_rating;
}


/* ==========================================
   STEP 2: NLP PROFILE
========================================== */

$pythonPath = "python";

$scriptPath = __DIR__ . "\\nlp\\analyze_diary.py";

$command = $pythonPath . " " . escapeshellarg($scriptPath);

$output = shell_exec($command);


/* ==========================================
   STEP 3: TF-IDF PROFILE
========================================== */

$tfidfScriptPath = __DIR__ . "\\nlp\\tfidf_analysis.py";

$tfidfCommand = $pythonPath . " " . escapeshellarg($tfidfScriptPath);

$tfidfOutput = shell_exec($tfidfCommand);


/* Default TF-IDF values */

$tfidfNature = 0;
$tfidfMountains = 0;
$tfidfBeach = 0;
$tfidfAdventure = 0;
$tfidfPeace = 0;
$tfidfCulture = 0;


/* Read only the final TF-IDF profile */

if ($tfidfOutput) {

    /*
       The Python program prints several values while
       analyzing the diaries.

       We only want the final TFIDF_PROFILE section.
    */

    $profileStart = strpos($tfidfOutput, "TFIDF_PROFILE");

    if ($profileStart !== false) {

        $profile = substr(
            $tfidfOutput,
            $profileStart
        );


        if (preg_match('/Nature\s*:\s*(-?\d+\.?\d*)/', $profile, $match)) {
            $tfidfNature = floatval($match[1]);
        }


        if (preg_match('/Mountains\s*:\s*(-?\d+\.?\d*)/', $profile, $match)) {
            $tfidfMountains = floatval($match[1]);
        }


        if (preg_match('/Beach\s*:\s*(-?\d+\.?\d*)/', $profile, $match)) {
            $tfidfBeach = floatval($match[1]);
        }


        if (preg_match('/Adventure\s*:\s*(-?\d+\.?\d*)/', $profile, $match)) {
            $tfidfAdventure = floatval($match[1]);
        }


        if (preg_match('/Peace\s*:\s*(-?\d+\.?\d*)/', $profile, $match)) {
            $tfidfPeace = floatval($match[1]);
        }


        if (preg_match('/Culture\s*:\s*(-?\d+\.?\d*)/', $profile, $match)) {
            $tfidfCulture = floatval($match[1]);
        }
    }
}


/* ==========================================
   STEP 4: Read NLP output
========================================== */

$nlpNature = 0;
$nlpMountains = 0;
$nlpBeach = 0;
$nlpAdventure = 0;
$nlpPeace = 0;
$nlpCulture = 0;

if ($output) {

    if (preg_match('/Nature\s*:\s*(-?\d+\.?\d*)/', $output, $match)) {
        $nlpNature = floatval($match[1]);
    }

    if (preg_match('/Mountains\s*:\s*(-?\d+\.?\d*)/', $output, $match)) {
        $nlpMountains = floatval($match[1]);
    }

    if (preg_match('/Beach\s*:\s*(-?\d+\.?\d*)/', $output, $match)) {
        $nlpBeach = floatval($match[1]);
    }

    if (preg_match('/Adventure\s*:\s*(-?\d+\.?\d*)/', $output, $match)) {
        $nlpAdventure = floatval($match[1]);
    }

    if (preg_match('/Peace\s*:\s*(-?\d+\.?\d*)/', $output, $match)) {
        $nlpPeace = floatval($match[1]);
    }

    if (preg_match('/Culture\s*:\s*(-?\d+\.?\d*)/', $output, $match)) {
        $nlpCulture = floatval($match[1]);
    }
}


/* ==========================================
   STEP 5: Normalize NLP profile
========================================== */

$nlpNature *= 5;
$nlpMountains *= 5;
$nlpBeach *= 5;
$nlpAdventure *= 5;
$nlpPeace *= 5;
$nlpCulture *= 5;


/* ==========================================
   STEP 6: Combine Rating + NLP
========================================== */

$ratingWeight = 0.70;
$nlpWeight = 0.30;

$nature =
    ($nature * $ratingWeight) +
    ($nlpNature * $nlpWeight);

$mountains =
    ($mountains * $ratingWeight) +
    ($nlpMountains * $nlpWeight);

$beach =
    ($beach * $ratingWeight) +
    ($nlpBeach * $nlpWeight);

$adventure =
    ($adventure * $ratingWeight) +
    ($nlpAdventure * $nlpWeight);

$peace =
    ($peace * $ratingWeight) +
    ($nlpPeace * $nlpWeight);

$culture =
    ($culture * $ratingWeight) +
    ($nlpCulture * $nlpWeight);


/* ==========================================
   STEP 7: Dynamic preference weights
========================================== */

$preferenceTotal =
    $nature +
    $mountains +
    $beach +
    $adventure +
    $peace +
    $culture;

if ($preferenceTotal > 0) {

    $weights = [

        "Nature" =>
            $nature / $preferenceTotal,

        "Mountains" =>
            $mountains / $preferenceTotal,

        "Beach" =>
            $beach / $preferenceTotal,

        "Adventure" =>
            $adventure / $preferenceTotal,

        "Peace" =>
            $peace / $preferenceTotal,

        "Culture" =>
            $culture / $preferenceTotal
    ];

} else {

    $weights = [

        "Nature" => 1/6,
        "Mountains" => 1/6,
        "Beach" => 1/6,
        "Adventure" => 1/6,
        "Peace" => 1/6,
        "Culture" => 1/6
    ];
}


/* ==========================================
   STEP 8: Get unvisited destinations
========================================== */

$destinationQuery = "
    SELECT * FROM destinations
    WHERE name NOT IN (
        SELECT destination
        FROM trips
        WHERE user_id = $user_id
    )
";

$destinationResult = $conn->query($destinationQuery);


/* ==========================================
   STEP 9: Calculate recommendations
========================================== */

$recommendations = [];

while ($destination = $destinationResult->fetch_assoc()) {

    $features = [

        "Nature" =>
            1 - (abs($nature - $destination['nature']) / 5),

        "Adventure" =>
            1 - (abs($adventure - $destination['adventure']) / 5),

        "Peace" =>
            1 - (abs($peace - $destination['peace']) / 5),

        "Culture" =>
            1 - (abs($culture - $destination['culture']) / 5),

        "Mountains" =>
            1 - (abs($mountains - $destination['mountains']) / 5),

        "Beach" =>
            1 - (abs($beach - $destination['beach']) / 5)
    ];


    /* Weighted final score */

    $finalScore = 0;

    foreach ($features as $feature => $similarity) {

        $finalScore +=
            $similarity * $weights[$feature];
    }

    $match = round($finalScore * 100, 2);


    /* ==========================================
       Smart explanations
    ========================================== */

    $reasons = [];

    $nlpPreferences = [

        "Nature" => $nlpNature,
        "Mountains" => $nlpMountains,
        "Beach" => $nlpBeach,
        "Adventure" => $nlpAdventure,
        "Peace" => $nlpPeace,
        "Culture" => $nlpCulture
    ];

    $explanationScore = [];

    foreach ($features as $feature => $similarity) {

        $nlpScore = $nlpPreferences[$feature];

        $score =
            ($similarity * 0.60) +
            (($nlpScore / 5) * 0.40);

        $explanationScore[$feature] = $score;
    }

    arsort($explanationScore);


    foreach ($explanationScore as $feature => $score) {

        $similarity = $features[$feature];

        $nlpScore = $nlpPreferences[$feature];

        if ($similarity < 0.65) {
            continue;
        }

        if ($nlpScore >= 3.5 && $similarity >= 0.75) {

            $reasons[] =
                $feature .
                " strongly matches interests found in your travel diary";

        } elseif ($nlpScore >= 2.0 && $similarity >= 0.75) {

            $reasons[] =
                $feature .
                " matches interests found in your travel diary";

        } elseif ($similarity >= 0.85) {

            $reasons[] =
                $feature .
                " is an excellent match for your preferences";

        } elseif ($similarity >= 0.70) {

            $reasons[] =
                $feature .
                " is a good match for your preferences";
        }

        if (count($reasons) == 3) {
            break;
        }
    }


    $recommendations[] = [

        "name" => $destination['name'],

        "match" => $match,

        "reasons" => $reasons
    ];
}


/* ==========================================
   STEP 10: Sort recommendations
========================================== */

usort($recommendations, function($a, $b) {

    return $b['match'] <=> $a['match'];

});


/* ==========================================
   Display data
========================================== */

$nlpProfile = [

    "Nature" => $nlpNature,
    "Mountains" => $nlpMountains,
    "Beach" => $nlpBeach,
    "Adventure" => $nlpAdventure,
    "Peace" => $nlpPeace,
    "Culture" => $nlpCulture
];

$tfidfProfile = [

    "Nature" => $tfidfNature,
    "Mountains" => $tfidfMountains,
    "Beach" => $tfidfBeach,
    "Adventure" => $tfidfAdventure,
    "Peace" => $tfidfPeace,
    "Culture" => $tfidfCulture
];


/* Icons */

$icons = [

    "Nature" => "🌿",
    "Mountains" => "⛰️",
    "Beach" => "🏖️",
    "Adventure" => "🧗",
    "Peace" => "🧘",
    "Culture" => "🏛️"
];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>WanderWise | AI Travel Dashboard</title>


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
        Inter,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #f0faff 0%,
            #f8fcff 45%,
            #eef9ff 100%
        );

    color: #17324d;

    min-height: 100vh;

    line-height: 1.6;
}


/* ==========================================
   NAVBAR
========================================== */

.navbar {

    width: 100%;

    background: rgba(255,255,255,0.92);

    backdrop-filter: blur(12px);

    border-bottom: 1px solid #d9eef8;

    position: sticky;

    top: 0;

    z-index: 100;
}

.nav-container {

    max-width: 1200px;

    margin: auto;

    padding: 18px 25px;

    display: flex;

    align-items: center;

    justify-content: space-between;
}

.logo {

    font-size: 24px;

    font-weight: 800;

    color: #087ea4;

    text-decoration: none;
}

.logo span {

    color: #23a6c7;
}

.nav-links {

    display: flex;

    gap: 10px;

    align-items: center;
}

.nav-links a {

    text-decoration: none;

    color: #587286;

    padding: 9px 15px;

    border-radius: 10px;

    font-size: 14px;

    font-weight: 600;

    transition: 0.25s;
}

.nav-links a:hover {

    background: #e8f7fc;

    color: #087ea4;
}

.nav-links .active {

    background: #dff5fb;

    color: #087ea4;
}


/* ==========================================
   MAIN CONTAINER
========================================== */

.dashboard {

    max-width: 1200px;

    margin: auto;

    padding: 45px 25px 70px;
}


/* ==========================================
   HERO
========================================== */

.hero {

    position: relative;

    overflow: hidden;

    background:
        linear-gradient(
            135deg,
            #dff7ff,
            #eefbff 55%,
            #d8f3fa
        );

    border: 1px solid #c7eaf5;

    border-radius: 28px;

    padding: 48px;

    margin-bottom: 30px;

    box-shadow:
        0 15px 45px rgba(32, 139, 170, 0.10);
}

.hero::after {

    content: "";

    position: absolute;

    width: 260px;

    height: 260px;

    border-radius: 50%;

    background: rgba(91, 206, 232, 0.16);

    right: -70px;

    top: -100px;
}

.hero-badge {

    display: inline-block;

    background: white;

    color: #087ea4;

    padding: 7px 13px;

    border-radius: 30px;

    font-size: 13px;

    font-weight: 700;

    margin-bottom: 16px;

    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.hero h1 {

    font-size: clamp(32px, 5vw, 52px);

    line-height: 1.1;

    color: #123b57;

    margin-bottom: 15px;
}

.hero h1 span {

    color: #1196b8;
}

.hero p {

    max-width: 650px;

    color: #5b7485;

    font-size: 17px;
}


/* ==========================================
   SECTION
========================================== */

.section {

    margin-top: 30px;
}

.section-heading {

    display: flex;

    justify-content: space-between;

    align-items: end;

    margin-bottom: 17px;

    gap: 20px;
}

.section-heading h2 {

    font-size: 25px;

    color: #173b55;
}

.section-heading p {

    color: #728899;

    font-size: 14px;

    margin-top: 3px;
}


/* ==========================================
   AI PROFILE CARD
========================================== */

.dashboard-card {

    background: rgba(255,255,255,0.94);

    border: 1px solid #dceef5;

    border-radius: 22px;

    padding: 28px;

    box-shadow:
        0 10px 30px rgba(24, 109, 138, 0.07);
}

.profile-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 18px;
}

.profile-item {

    background: #f8fcfe;

    border: 1px solid #e2f2f7;

    border-radius: 17px;

    padding: 20px;

    transition: 0.25s;
}

.profile-item:hover {

    transform: translateY(-3px);

    box-shadow:
        0 10px 25px rgba(35, 150, 180, 0.10);
}

.profile-top {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 13px;
}

.feature-name {

    display: flex;

    gap: 8px;

    align-items: center;

    font-weight: 700;

    color: #31566d;
}

.feature-icon {

    font-size: 22px;
}

.profile-percent {

    font-size: 20px;

    font-weight: 800;

    color: #0785a8;
}

.progress {

    height: 9px;

    background: #e3f1f5;

    border-radius: 20px;

    overflow: hidden;
}

.progress-bar {

    height: 100%;

    border-radius: 20px;

    background:
        linear-gradient(
            90deg,
            #29b6d3,
            #0785a8
        );

    transition: width 0.8s ease;
}


/* ==========================================
   TF-IDF
========================================== */

.insight-card {

    background:
        linear-gradient(
            135deg,
            #ffffff,
            #f3fbfe
        );

    border: 1px solid #dceff5;

    border-radius: 22px;

    padding: 28px;

    box-shadow:
        0 10px 30px rgba(24, 109, 138, 0.06);
}

.insight-grid {

    display: grid;

    grid-template-columns:
        repeat(6, 1fr);

    gap: 14px;

    margin-top: 22px;
}

.insight-item {

    text-align: center;

    padding: 17px 10px;

    border-radius: 16px;

    background: #f7fcfe;

    border: 1px solid #e2f1f6;
}

.insight-value {

    font-size: 23px;

    font-weight: 800;

    color: #167d9d;

    display: block;
}

.insight-name {

    font-size: 12px;

    color: #718796;

    font-weight: 600;

    margin-top: 4px;
}


/* ==========================================
   RECOMMENDATIONS
========================================== */

.recommendation-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 22px;
}

.destination-card {

    background: white;

    border: 1px solid #dceef5;

    border-radius: 23px;

    overflow: hidden;

    box-shadow:
        0 12px 30px rgba(23, 103, 130, 0.08);

    transition:
        transform 0.25s,
        box-shadow 0.25s;
}

.destination-card:hover {

    transform: translateY(-7px);

    box-shadow:
        0 20px 40px rgba(23, 103, 130, 0.14);
}

.destination-top {

    background:
        linear-gradient(
            135deg,
            #e3f8fd,
            #f4fcff
        );

    padding: 25px;

    position: relative;
}

.destination-number {

    position: absolute;

    right: 20px;

    top: 20px;

    background: white;

    color: #6b8796;

    border-radius: 50%;

    width: 32px;

    height: 32px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 13px;

    font-weight: 700;
}

.destination-icon {

    font-size: 37px;

    margin-bottom: 8px;
}

.destination-name {

    font-size: 25px;

    font-weight: 800;

    color: #163e57;
}

.match-badge {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    margin-top: 12px;

    background: #d9f6ee;

    color: #16745c;

    padding: 6px 11px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: 800;
}

.destination-body {

    padding: 24px;
}

.why-title {

    font-size: 13px;

    text-transform: uppercase;

    letter-spacing: 0.8px;

    color: #8295a1;

    font-weight: 800;

    margin-bottom: 12px;
}

.reason {

    display: flex;

    gap: 9px;

    color: #526b7a;

    font-size: 14px;

    margin-bottom: 10px;

    line-height: 1.5;
}

.reason-icon {

    color: #12a080;

    font-weight: bold;
}


/* ==========================================
   FOOTER ACTIONS
========================================== */

.actions {

    display: flex;

    justify-content: center;

    gap: 14px;

    margin-top: 35px;

    flex-wrap: wrap;
}

.action-button {

    text-decoration: none;

    padding: 12px 21px;

    border-radius: 12px;

    font-size: 14px;

    font-weight: 700;

    transition: 0.25s;
}

.primary-button {

    background: #087ea4;

    color: white;

    box-shadow:
        0 8px 18px rgba(8, 126, 164, 0.20);
}

.primary-button:hover {

    background: #066c8d;

    transform: translateY(-2px);
}

.secondary-button {

    background: white;

    color: #087ea4;

    border: 1px solid #cce7ef;
}

.secondary-button:hover {

    background: #edfaff;
}


/* ==========================================
   FOOTER
========================================== */

.footer {

    text-align: center;

    color: #8295a1;

    font-size: 13px;

    padding: 30px 0 10px;
}


/* ==========================================
   RESPONSIVE
========================================== */

@media (max-width: 950px) {

    .recommendation-grid {

        grid-template-columns:
            repeat(2, 1fr);
    }

    .profile-grid {

        grid-template-columns:
            repeat(2, 1fr);
    }

    .insight-grid {

        grid-template-columns:
            repeat(3, 1fr);
    }
}


@media (max-width: 650px) {

    .nav-container {

        flex-direction: column;

        gap: 12px;
    }

    .nav-links {

        flex-wrap: wrap;

        justify-content: center;
    }

    .dashboard {

        padding: 25px 15px 50px;
    }

    .hero {

        padding: 30px 23px;

        border-radius: 22px;
    }

    .hero h1 {

        font-size: 34px;
    }

    .profile-grid,
    .recommendation-grid {

        grid-template-columns: 1fr;
    }

    .insight-grid {

        grid-template-columns:
            repeat(2, 1fr);
    }

    .dashboard-card,
    .insight-card {

        padding: 20px;
    }
}

</style>

</head>


<body>


<!-- ==========================================
     NAVIGATION
========================================== -->

<nav class="navbar">

    <div class="nav-container">

        <a href="recommend.php" class="logo">
            🌊 Wander<span>Wise</span>
        </a>

        <div class="nav-links">

            <a href="my_trips.php">
                My Trips
            </a>

            <a href="preferences.php">
                Preferences
            </a>

            <a href="recommend.php" class="active">
                AI Recommendations
            </a>

        </div>

    </div>

</nav>



<main class="dashboard">


<!-- ==========================================
     HERO
========================================== -->

<section class="hero">

    <div class="hero-badge">
        ✨ AI-Powered Travel Intelligence
    </div>

    <h1>
        Discover your next
        <span>perfect escape.</span>
    </h1>

    <p>
        WanderWise analyzes your travel history, ratings and diary entries
        to understand your travel personality and discover destinations
        that match your interests.
    </p>

</section>



<!-- ==========================================
     AI TRAVEL PROFILE
========================================== -->

<section class="section">

    <div class="section-heading">

        <div>

            <h2>
                🤖 Your AI Travel Profile
            </h2>

            <p>
                Built from your previous travel diary using NLP analysis.
            </p>

        </div>

    </div>


    <div class="dashboard-card">

        <div class="profile-grid">

            <?php foreach ($nlpProfile as $feature => $score): ?>

                <?php

                $percentage = round($score * 20);

                $percentage = max(
                    0,
                    min(100, $percentage)
                );

                ?>

                <div class="profile-item">

                    <div class="profile-top">

                        <div class="feature-name">

                            <span class="feature-icon">
                                <?php echo $icons[$feature]; ?>
                            </span>

                            <?php echo htmlspecialchars($feature); ?>

                        </div>

                        <span class="profile-percent">
                            <?php echo $percentage; ?>%
                        </span>

                    </div>


                    <div class="progress">

                        <div
                            class="progress-bar"
                            style="width: <?php echo $percentage; ?>%;"
                        ></div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>



<!-- ==========================================
     TF-IDF INSIGHTS
========================================== -->

<section class="section">

    <div class="section-heading">

        <div>

            <h2>
                🔎 Diary Insights
            </h2>

            <p>
                TF-IDF highlights the travel themes appearing most prominently
                in your diary entries.
            </p>

        </div>

    </div>


    <div class="insight-card">

        <div class="insight-grid">

            <?php foreach ($tfidfProfile as $feature => $score): ?>

                <?php

                $percentage = max(
                    0,
                    min(100, round($score))
                );

                ?>

                <div class="insight-item">

                    <span class="insight-value">

                        <?php echo $percentage; ?>%

                    </span>

                    <span class="insight-name">

                        <?php echo htmlspecialchars($feature); ?>

                    </span>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>



<!-- ==========================================
     RECOMMENDATIONS
========================================== -->

<section class="section">

    <div class="section-heading">

        <div>

            <h2>
                ✈️ Recommended For You
            </h2>

            <p>
                Destinations selected based on your personalized travel profile.
            </p>

        </div>

    </div>


    <div class="recommendation-grid">


        <?php

        $rank = 1;

        foreach ($recommendations as $recommendation):

        ?>


            <article class="destination-card">


                <!-- Destination Header -->

                <div class="destination-top">

                    <div class="destination-number">

                        #<?php echo $rank; ?>

                    </div>


                    <div class="destination-icon">

                        <?php

                        if ($recommendation['name'] == "Meghalaya") {
                            echo "🌿";
                        } elseif ($recommendation['name'] == "Kerala") {
                            echo "🌴";
                        } elseif ($recommendation['name'] == "Andaman") {
                            echo "🏝️";
                        } elseif ($recommendation['name'] == "Kashmir") {
                            echo "🏔️";
                        } elseif ($recommendation['name'] == "Rajasthan") {
                            echo "🏜️";
                        } else {
                            echo "🌍";
                        }

                        ?>

                    </div>


                    <div class="destination-name">

                        <?php

                        echo htmlspecialchars(
                            $recommendation['name']
                        );

                        ?>

                    </div>


                    <div class="match-badge">

                        ✦

                        <?php

                        echo $recommendation['match'];

                        ?>%

                        Personal Match

                    </div>

                </div>



                <!-- Destination Explanation -->

                <div class="destination-body">

                    <div class="why-title">

                        Why this destination?

                    </div>


                    <?php if (count($recommendation['reasons']) > 0): ?>


                        <?php foreach ($recommendation['reasons'] as $reason): ?>

                            <div class="reason">

                                <span class="reason-icon">
                                    ✓
                                </span>

                                <span>

                                    <?php

                                    echo htmlspecialchars(
                                        $reason
                                    );

                                    ?>

                                </span>

                            </div>

                        <?php endforeach; ?>


                    <?php else: ?>


                        <div class="reason">

                            <span class="reason-icon">
                                ✓
                            </span>

                            <span>
                                Overall preferences are reasonably similar.
                            </span>

                        </div>


                    <?php endif; ?>

                </div>

            </article>


        <?php

            $rank++;

        endforeach;

        ?>

    </div>

</section>



<!-- ==========================================
     ACTION BUTTONS
========================================== -->

<div class="actions">

    <a
        href="my_trips.php"
        class="action-button secondary-button"
    >
        ← View My Trips
    </a>


    <a
        href="preferences.php"
        class="action-button primary-button"
    >
        View My Preferences →
    </a>

</div>


<!-- ==========================================
     FOOTER
========================================== -->

<footer class="footer">

    <p>
        WanderWise · Personalized Travel Intelligence
    </p>

    <p>
        Built with PHP · MySQL · NLP · TF-IDF
    </p>

</footer>


</main>


</body>

</html>