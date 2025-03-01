<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Table</title>
    <link rel="stylesheet" href="../CSS/Display.css">
</head>
<body>
    <header>
        <div class="logo">DL</div>
        <button class="org-btn">Org</button>
    </header>
    
    <div class="container">
        <div class="controls">
            <select id="filter">
                <option>Filter</option>
                <option value="all">All</option>
                <option value="type1">Type 1</option>
                <option value="type2">Type 2</option>
            </select>
            <input type="text" id="search" placeholder="Search...">
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Org Name</th>
                    <th>Donation Type</th>
                </tr>
            </thead>
            <tbody>
                <!-- <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "your_database");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                
                // Fetch data
                $sql = "SELECT name, org_name, donation_type FROM organizations";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['name']}</td>
                                <td>{$row['org_name']}</td>
                                <td>{$row['donation_type']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No records found</td></tr>";
                }
                $conn->close();
                ?> -->
            </tbody>
        </table>
    </div>
</body>
</html>
