<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mobile scaling -->
    <title>Facility Survey</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #32418C;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .survey-container {
            width: 95%;
            max-width: 600px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #32418C;
            margin-bottom: 20px;
            font-size: 22px;
        }

        label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
            color: #333;
            font-size: 14px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #32418C;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 25px;
            font-size: 16px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #283373;
        }

        @media (max-width: 480px) {
            .survey-container {
                padding: 20px 15px;
            }

            h1 {
                font-size: 20px;
            }

            label {
                font-size: 13px;
            }

            select {
                font-size: 13px;
            }

            .submit-btn {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="survey-container">
        <h1>Facility Experience Survey</h1>
        <form action="submit_survey.php" method="POST">
            <!-- Hidden field for facility_id -->
            <input type="hidden" name="facility_id" value="<?php echo isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0; ?>">

            <label>What made you satisfied or dissatisfied?</label>
            <select name="q1" required>
                <option value="">Select...</option>
                <option value="Cleanliness">Cleanliness</option>
                <option value="Staff behavior">Staff behavior</option>
                <option value="Facilities condition">Facilities condition</option>
                <option value="Speed of service">Speed of service</option>
                <option value="Other">Other</option>
            </select>

            <label>How can we enhance your experience?</label>
            <select name="q2" required>
                <option value="">Select...</option>
                <option value="Improve cleanliness">Improve cleanliness</option>
                <option value="Faster service">Faster service</option>
                <option value="More staff presence">More staff presence</option>
                <option value="Upgrade facilities">Upgrade facilities</option>
                <option value="Better signage or info">Better signage or info</option>
            </select>

            <label>Were the facilities clean and well-maintained?</label>
            <select name="q3" required>
                <option value="">Select...</option>
                <option value="Yes, very clean">Yes, very clean</option>
                <option value="Somewhat clean">Somewhat clean</option>
                <option value="Not clean">Not clean</option>
                <option value="I did not check">I did not check</option>
            </select>

            <label>Was it easy to locate the facility?</label>
            <select name="q4" required>
                <option value="">Select...</option>
                <option value="Yes, very easy">Yes, very easy</option>
                <option value="Somewhat easy">Somewhat easy</option>
                <option value="Difficult to find">Difficult to find</option>
            </select>

            <label>Were the staff courteous and helpful?</label>
            <select name="q5" required>
                <option value="">Select...</option>
                <option value="Very courteous">Very courteous</option>
                <option value="Somewhat courteous">Somewhat courteous</option>
                <option value="Not courteous">Not courteous</option>
                <option value="Did not interact with staff">Did not interact with staff</option>
            </select>

            <label>Did you experience any delays or issues?</label>
            <select name="q6" required>
                <option value="">Select...</option>
                <option value="No delays">No delays</option>
                <option value="Minor delays">Minor delays</option>
                <option value="Major delays">Major delays</option>
                <option value="Did not require assistance">Did not require assistance</option>
            </select>

            <label>Was the environment comfortable?</label>
            <select name="q7" required>
                <option value="">Select...</option>
                <option value="Yes, very comfortable">Yes, very comfortable</option>
                <option value="Somewhat comfortable">Somewhat comfortable</option>
                <option value="Not comfortable">Not comfortable</option>
            </select>

            <label>Did you feel safe and secure?</label>
            <select name="q8" required>
                <option value="">Select...</option>
                <option value="Yes">Yes</option>
                <option value="Somewhat">Somewhat</option>
                <option value="No">No</option>
            </select>

            <label>Were the equipment and resources in good condition?</label>
            <select name="q9" required>
                <option value="">Select...</option>
                <option value="Yes, all working well">Yes, all working well</option>
                <option value="Some were not functional">Some were not functional</option>
                <option value="Most were broken">Most were broken</option>
            </select>

            <label>Any suggestions to help us improve?</label>
            <select name="q10" required>
                <option value="">Select...</option>
                <option value="More frequent maintenance">More frequent maintenance</option>
                <option value="Better customer service">Better customer service</option>
                <option value="Upgrade furniture or equipment">Upgrade furniture or equipment</option>
                <option value="Other">Other</option>
            </select>

            <button type="submit" class="submit-btn">Submit Survey</button>
        </form>
    </div>
</body>
</html>
