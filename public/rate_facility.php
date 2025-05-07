<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?facility_id=" . urlencode($_GET['facility_id'] ?? ""));
    exit();
}

$facility_id = $_GET['facility_id'] ?? null;
if (!$facility_id) {
    die("<p style='color: red; text-align: center;'>Invalid Facility ID.</p>");
}

$sql = "SELECT name FROM facilities WHERE facility_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $facility_id);
$stmt->execute();
$result = $stmt->get_result();
$facility = $result->fetch_assoc();

if (!$facility) {
    die("<p style='color: red; text-align: center;'>Facility not found.</p>");
}

$announcements = [];
$today = date("Y-m-d");
$sql = "SELECT title, description FROM announcements WHERE end_date >= ? ORDER BY end_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

// Get all facilities for "Rate Another Facility"
$all_facilities = [];
$sql = "SELECT facility_id, name FROM facilities";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $all_facilities[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Facility - <?php echo htmlspecialchars($facility['name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #32418C;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .rating-container {
            width: 90%;
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 70px;
        }
        h1 { text-align: center; color: #32418C; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        textarea, .anonymous-checkbox {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }
        .star-rating span {
            font-size: 30px;
            cursor: pointer;
            color: gray;
        }
        .star-rating .selected { color: gold; }
        .request-btn {
            width: 10%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: bold;
        }
        .submit-btn {
            background: #32418C;
            color: white;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: bold;
        }
        .submit-btn:hover { background: #283373; }
        .request-btn {
            background: #FBD117;
            color: black;
        }
        .request-btn:hover { background: #e0b800; }
        .anonymous-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .announcement-banner {
            width: 100%;
            background: #FBD117;
            color: black;
            padding: 10px 0;
            text-align: center;
            font-weight: bold;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .announcement-item {
            font-size: 16px;
            white-space: nowrap;
        }
        .no-announcement {
            text-align: center;
            font-size: 14px;
            padding: 10px;
            font-weight: bold;
        }

        /* Modal Styles */
        #successModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #successModalContent {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        #successModalContent button {
            margin: 10px;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .proceed-btn {
            background-color: #28a745;
            color: white;
        }

        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="announcement-banner">
    <?php if (!empty($announcements)): ?>
        <marquee behavior="scroll" direction="left">
            <?php foreach ($announcements as $announcement): ?>
                <span class="announcement-item">
                    <strong><?php echo htmlspecialchars($announcement['title']); ?>:</strong> 
                    <?php echo htmlspecialchars($announcement['description']); ?> &nbsp;&nbsp;|&nbsp;&nbsp;
                </span>
            <?php endforeach; ?>
        </marquee>
    <?php else: ?>
        <p class="no-announcement">No announcements available.</p>
    <?php endif; ?>
</div>

<div class="rating-container">
    <h1>Rate <?php echo htmlspecialchars($facility['name']); ?></h1>
    <form id="ratingForm">
        <input type="hidden" name="facility_id" value="<?php echo $facility_id; ?>">
        <label for="rating">Rating:</label>
        <div class="star-rating">
            <input type="hidden" name="rating" id="rating" required>
            <span class="star" onclick="setRating(1)">&#9733;</span>
            <span class="star" onclick="setRating(2)">&#9733;</span>
            <span class="star" onclick="setRating(3)">&#9733;</span>
            <span class="star" onclick="setRating(4)">&#9733;</span>
            <span class="star" onclick="setRating(5)">&#9733;</span>
            <span class="star" onclick="setRating(6)">&#9733;</span>
            <span class="star" onclick="setRating(7)">&#9733;</span>
        </div>
        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment" placeholder="Write your feedback..." required></textarea>

        <div class="anonymous-container">
            <input type="checkbox" name="anonymous" id="anonymous" value="1">
            <label for="anonymous">Rate Anonymously</label>
        </div>

        <button type="submit" class="submit-btn">Submit Rating</button>
    </form>
</div>

<!-- Modal -->
<div id="successModal">
    <div id="successModalContent">
        <h2 style="color: #32418C;">Thank you for your feedback!</h2>
        <p>Help us make the offices better. Answer these questions and receive discount coupons in NU Exchange or cafeteria.</p>
        <button class="proceed-btn">Proceed</button>
        <button class="cancel-btn" onclick="closeModal()">No, I’m good</button>
    </div>
</div>

<button class="request-btn" onclick="openFacilityModal()">Rate Another Facility</button>

<!-- Modal for QR Code List -->
<div id="facilityModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:1002; align-items:center; justify-content:center;">
    <div style="background:white; padding:20px; max-width:90%; max-height:90%; overflow-y:auto; border-radius:10px;">
        <h2 style="text-align:center; color:#32418C;">Select a Facility</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            <?php foreach ($all_facilities as $fac): ?>
                <div onclick="redirectToFacility('<?php echo $fac['facility_id']; ?>')" style="cursor:pointer; text-align:center;">
                    <img src="generate_qr.php?facility_id=<?php echo $fac['facility_id']; ?>" alt="QR" width="100">
                    <p style="color:#32418C; font-weight:bold;"><?php echo htmlspecialchars($fac['name']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top: 20px;">
            <button onclick="closeFacilityModal()" style="background:#dc3545; color:white; padding:10px 20px; border:none; border-radius:5px;">Close</button>
        </div>
    </div>
</div>


<script>
    function setRating(stars) {
        document.getElementById("rating").value = stars;
        document.querySelectorAll(".star").forEach((star, index) => {
            star.classList.toggle("selected", index < stars);
        });
    }

    function closeModal() {
        document.getElementById("successModal").style.display = "none";
    }

    document.getElementById("ratingForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch("submit_rating.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("successModal").style.display = "flex";
                form.reset();
                document.querySelectorAll(".star").forEach(star => star.classList.remove("selected"));
            } else {
                alert(data.error || "Failed to submit rating.");
            }
        })
        .catch(error => {
            alert("Error submitting rating.");
            console.error(error);
        });
    });

    // Add the event listener for the "Proceed" button
    document.querySelector(".proceed-btn").addEventListener("click", function() {
        // Fetch the facility_id from the hidden input field
        const facilityId = document.querySelector('input[name="facility_id"]').value;
        
        // Redirect to survey.php and pass the facility_id as a query parameter
        window.location.href = 'survey.php?facility_id=' + encodeURIComponent(facilityId);
    });
</script>
<script>
    function openFacilityModal() {
        document.getElementById('facilityModal').style.display = 'flex';
    }

    function closeFacilityModal() {
        document.getElementById('facilityModal').style.display = 'none';
    }

    function redirectToFacility(facilityId) {
        window.location.href = 'rate_facility.php?facility_id=' + encodeURIComponent(facilityId);
    }
</script>

</body>
</html>
