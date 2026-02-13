<?php
    require_once("data/db.php");
    session_start();
    session_destroy();
    
    $limit = 3;

    $schoolID = isset($_GET['schoolID']) ? (int)$_GET['schoolID'] : 0;
    $departmentID = isset($_GET['departmentID']) ? (int)$_GET['departmentID'] : 0;
    $programID = isset($_GET['progid']) ? (int)$_GET['progid'] : 0;

    # $dbStatement = $db->prepare("SELECT * FROM students");
    # $dbStatement->execute();
    # $totalStudents = $dbStatement->rowCount();

    $dbStatement = $db->prepare("
    SELECT COUNT(*) 
    FROM students 
    WHERE studcollid = :schoolID 
        AND studcolldeptid = :departmentID 
        AND studprogid = :programID
    ");

    $dbStatement->execute([
        ':schoolID' => $schoolID,
        ':departmentID' => $departmentID,
        ':programID' => $programID
    ]);
    
    $totalStudents = $dbStatement->fetchColumn();

    $totalPages = ceil($totalStudents / $limit);

    if(!isset($_GET['pgSection']) || !is_numeric($_GET['pgSection'])) {
        $currentPage = 1;
    } else {
        $currentPage = intval($_GET['pgSection']);
    }

    $offset = ($currentPage - 1) * $limit;

    // $dbStatement = $db->prepare("SELECT * FROM students ORDER BY studid LIMIT :offset, :limit;");
    // $dbStatement->bindParam('offset', $offset, PDO::PARAM_INT);
    // $dbStatement->bindParam('limit', $limit, PDO::PARAM_INT);
    // $dbStatement->execute();

    $dbStatement = $db->prepare("
    SELECT * 
    FROM students 
    WHERE studcollid = :schoolID
        AND studcolldeptid = :departmentID
        AND studprogid = :programID
    ORDER BY studid 
    LIMIT :offset, :limit
    ");

    $dbStatement->bindValue(':schoolID', $schoolID, PDO::PARAM_INT);
    $dbStatement->bindValue(':departmentID', $departmentID, PDO::PARAM_INT);
    $dbStatement->bindValue(':programID', $programID, PDO::PARAM_INT);
    $dbStatement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $dbStatement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $dbStatement->execute();

    $students = $dbStatement->fetchAll();

//     $dbStatement->execute(['offset' => $offset, 'limit' => $limit]);
    
?>

<h1>Student List</h1>
<div>
    <h2><a href="index.php?section=student&page=studentAdd&schoolID=<?= $schoolID ?>&departmentID=<?= $departmentID ?>&progid=<?= $programID ?>" class="btn btn-primary">Add Student</a></h2>
</div>
<table>
    <tr>
        <th>Student ID</th>
        <th>Last Name</th>
        <th>First Name</th>
    </tr>
    <?php foreach ($students as $student): ?>
    <tr>
        <td><?php echo $student['studid']; ?></td>
        <td><?php echo $student['studlastname']; ?></td>
        <td><?php echo $student['studfirstname']; ?></td>
        <td>
            <a href="index.php?section=student&page=studentUpdate&schoolID=<?= $schoolID ?>&departmentID=<?= $departmentID ?>&progid=<?= $programID ?>&studid=<?php echo $student['studid']; ?>" class="btn btn-primary">Update</a>
            <a href="index.php?section=student&page=studentDelete&schoolID=<?= $schoolID ?>&departmentID=<?= $departmentID ?>&progid=<?= $programID ?>&studid=<?php echo $student['studid']; ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="2">
            <span>
                Total of: <?= $totalStudents ?> students in the database
            </span>
        </td>
        <td colspan="2">
        <?php if($totalPages > 1): ?>  
            <?php if ($currentPage > 1): ?>
                <a href="index.php?section=student&page=studentList&schoolID=<?= $schoolID ?>&departmentID=<?= $departmentID ?>&progid=<?= $programID ?>&pgSection=<?= $currentPage - 1 ?>" class="btn btn-primary">Previous</a>
                <!-- <a href="index.php?section=student&page=studentList&pgSection= $currentPage + 1 " class="btn btn-primary">Previous</a> -->
            <?php else: ?>
                <span>Previous</span>
            <?php endif; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="index.php?section=student&page=studentList&schoolID=<?= $schoolID ?>&departmentID=<?= $departmentID ?>&progid=<?= $programID ?>&pgSection=<?= $currentPage - 1 ?>" class="btn btn-primary">Next</a>
                <!-- <a href="index.php?section=student&page=studentList&pgSection= $currentPage + 1" class="btn btn-primary">Next</a> -->
            <?php else: ?>
                <span>Next</span>
            <?php endif; ?>
        <?php endif; ?>  
        </td>
    </tr>
</table>