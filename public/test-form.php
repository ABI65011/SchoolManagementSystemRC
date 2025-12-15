<?php
/* public/test-form.php  –  quick sanity check for student registration */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<h2>Raw $_POST</h2><pre>' . print_r($_POST, 1) . '</pre>';
    echo '<h2>Raw $_FILES</h2><pre>' . print_r($_FILES, 1) . '</pre>';
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Student reg test</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 40px;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            max-width: 500px;
            width: 100%;
            padding: 6px;
        }

        button {
            margin-top: 25px;
            padding: 10px 20px;
        }
    </style>
</head>

<body>
    <h1>Student registration – TEST FORM</h1>
    <form method="post" enctype="multipart/form-data">
        @csrf

        <!-- 1. Core bio / admission -->
        <label>Admission year</label>
        <input type="number" name="admission_year" value="<?= date('Y') ?>" required>

        <label>Joining class</label>
        <input type="text" name="joining_class" placeholder="e.g. S.1" required>

        <label>A-Level combination</label>
        <input type="text" name="a_level_combination" placeholder="e.g. PCM">

        <label>Applying section</label>
        <select name="applying_section" required>
            <option value="">-- select --</option>
            <option>Day</option>
            <option>Boarding</option>
        </select>

        <label>First name</label>
        <input type="text" name="first_name" required>

        <label>Middle name</label>
        <input type="text" name="middle_name">

        <label>Last name</label>
        <input type="text" name="last_name" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">-- select --</option>
            <option>Male</option>
            <option>Female</option>
        </select>

        <label>Date of birth</label>
        <input type="date" name="dob" required>

        <label>Citizenship (multi)</label>
        <select name="citizenship[]" multiple required>
            <option value="UG">Uganda</option>
            <option value="KE">Kenya</option>
            <option value="TZ">Tanzania</option>
        </select>

        <label>Religious affiliation</label>
        <select name="religious_affiliation" required>
            <option value="">-- select --</option>
            <option>Christian</option>
            <option>Muslim</option>
            <option>Other</option>
        </select>

        <label>Spoken languages (multi)</label>
        <select name="spoken_languages[]" multiple required>
            <option value="en">English</option>
            <option value="sw">Swahili</option>
            <option value="lg">Luganda</option>
        </select>

        <label>ID type</label>
        <select name="id_type" required>
            <option value="">-- select --</option>
            <option>National ID</option>
            <option>Passport</option>
            <option>Birth cert</option>
        </select>

        <label>ID no</label>
        <input type="text" name="id_no" required>

        <label>ID image ≤2 MB</label>
        <input type="file" name="id_image_path" accept="image/*" required>

        <!-- 2. Academic history (first block) -->
        <h3>Academic history (index 0)</h3>
        <label>Academic level</label>
        <select name="academic_history[0][academic_level]" required>
            <option value="">-- select --</option>
            <option>PLE</option>
            <option>UCE</option>
            <option>UACE</option>
        </select>

        <label>School name</label>
        <input type="text" name="academic_history[0][school_name]" required>

        <label>From year</label>
        <input type="number" name="academic_history[0][from_year]" placeholder="YYYY" required>

        <label>To year</label>
        <input type="number" name="academic_history[0][to_year]" placeholder="YYYY" required>

        <label>Aggregate score</label>
        <input type="text" name="academic_history[0][aggregate_score]" required>

        <label>Grade</label>
        <input type="text" name="academic_history[0][grade]">

        <!-- 3. Health / discipline -->
        <label>Has health issues?</label>
        <select name="has_health_issues" required>
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>

        <label>Health details</label>
        <textarea name="health_issues" rows="3"></textarea>

        <label>Upload medical files (multi)</label>
        <input type="file" name="medical_files[]" multiple>

        <label>Has disciplinary issues?</label>
        <select name="has_disciplinary_issues" required>
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>

        <label>Disciplinary action</label>
        <select name="disciplinary_issues">
            <option value="">-- select --</option>
            <option>Warning</option>
            <option>Suspension</option>
            <option>Expulsion</option>
        </select>

        <label>Reason / details</label>
        <textarea name="reason" rows="3"></textarea>

        <!-- 4. Career -->
        <label>Career aspiration</label>
        <select name="aspiration">
            <option value="">-- select --</option>
            <option>Engineer</option>
            <option>Doctor</option>
            <option>Teacher</option>
        </select>

        <label>Best-done subject</label>
        <select name="best_done_subjects">
            <option value="">-- select --</option>
            <option>Mathematics</option>
            <option>Physics</option>
            <option>Biology</option>
        </select>

        <label>Additional info</label>
        <textarea name="additional_info" rows="4"></textarea>

        <button type="submit">Dump POST</button>
    </form>
</body>

</html>
