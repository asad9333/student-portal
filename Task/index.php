<?php
session_start();

// Initialize students array in session if not exists
if (!isset($_SESSION['students'])) {
    $_SESSION['students'] = [];
}

// Handle form submissions
$action_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        // Add Student
        if ($_POST['action'] === 'add') {
            $student = [
                'id' => uniqid(),
                'name' => htmlspecialchars($_POST['name']),
                'email' => htmlspecialchars($_POST['email']),
                'course' => htmlspecialchars($_POST['course']),
                'grade' => htmlspecialchars($_POST['grade'])
            ];
            $_SESSION['students'][] = $student;
            header('Location: index.php?action=added');
            exit;
        }
        
        // Update Student
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            foreach ($_SESSION['students'] as &$student) {
                if ($student['id'] === $id) {
                    $student['name'] = htmlspecialchars($_POST['name']);
                    $student['email'] = htmlspecialchars($_POST['email']);
                    $student['course'] = htmlspecialchars($_POST['course']);
                    $student['grade'] = htmlspecialchars($_POST['grade']);
                    break;
                }
            }
            header('Location: index.php?action=updated');
            exit;
        }
    }
}

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['students'] = array_filter($_SESSION['students'], function($student) use ($id) {
        return $student['id'] !== $id;
    });
    header('Location: index.php?action=deleted');
    exit;
}

// Get action message for display
$action_message = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action === 'added') {
        $action_message = 'Student record has been added successfully!';
    } elseif ($action === 'updated') {
        $action_message = 'Student record has been updated successfully!';
    } elseif ($action === 'deleted') {
        $action_message = 'Student record has been deleted successfully!';
    }
}

// Get student for editing
$edit_student = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($_SESSION['students'] as $student) {
        if ($student['id'] === $id) {
            $edit_student = $student;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0d9488 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 10px;
        }

        .header p {
            color: #a7f3d0;
            font-size: 1.1rem;
        }

        .action-message {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .card h2 {
            color: #0d9488;
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #374151;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d9488, #14b8a6);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0f766e, #0d9488);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13, 148, 136, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #9ca3af);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #f97316);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: white;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706, #f59e0b);
        }

        .form-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: linear-gradient(135deg, #0d9488, #14b8a6);
            color: white;
            font-weight: 600;
        }

        th:first-child {
            border-top-left-radius: 10px;
        }

        th:last-child {
            border-top-right-radius: 10px;
        }

        tr:hover {
            background: #f9fafb;
        }

        td {
            color: #374151;
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .action-btns .btn {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #9ca3af;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #0d9488, #14b8a6);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        .stat-card p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .action-btns {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Student Portal</h1>
            <p>Manage student records with ease</p>
        </div>

        <?php if ($action_message): ?>
            <div class="action-message">
                <?php echo $action_message; ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo count($_SESSION['students']); ?></h3>
                <p>Total Students</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count(array_filter($_SESSION['students'], function($s) { return $s['grade'] === 'A'; })); ?></h3>
                <p>Grade A Students</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count(array_filter($_SESSION['students'], function($s) { return $s['grade'] === 'B'; })); ?></h3>
                <p>Grade B Students</p>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="card">
            <h2><?php echo $edit_student ? '✏️ Edit Student' : '➕ Add New Student'; ?></h2>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="<?php echo $edit_student ? 'update' : 'add'; ?>">
                <?php if ($edit_student): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo $edit_student ? $edit_student['name'] : ''; ?>"
                               placeholder="Enter student name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo $edit_student ? $edit_student['email'] : ''; ?>"
                               placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label for="course">Course</label>
                        <input type="text" id="course" name="course" required 
                               value="<?php echo $edit_student ? $edit_student['course'] : ''; ?>"
                               placeholder="Enter course name">
                    </div>
                    <div class="form-group">
                        <label for="grade">Grade</label>
                        <select id="grade" name="grade" required>
                            <option value="">Select Grade</option>
                            <option value="A" <?php echo ($edit_student && $edit_student['grade'] === 'A') ? 'selected' : ''; ?>>A - Excellent</option>
                            <option value="B" <?php echo ($edit_student && $edit_student['grade'] === 'B') ? 'selected' : ''; ?>>B - Good</option>
                            <option value="C" <?php echo ($edit_student && $edit_student['grade'] === 'C') ? 'selected' : ''; ?>>C - Average</option>
                            <option value="D" <?php echo ($edit_student && $edit_student['grade'] === 'D') ? 'selected' : ''; ?>>D - Below Average</option>
                            <option value="F" <?php echo ($edit_student && $edit_student['grade'] === 'F') ? 'selected' : ''; ?>>F - Fail</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_student ? '✓ Update Student' : '➕ Add Student'; ?>
                    </button>
                    <?php if ($edit_student): ?>
                        <a href="index.php" class="btn btn-secondary">✕ Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Student Records Table -->
        <div class="card">
            <h2>📋 Student Records</h2>
            
            <?php if (empty($_SESSION['students'])): ?>
                <div class="empty-state">
                    <h3>No Students Found</h3>
                    <p>Add your first student using the form above!</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['students'] as $index => $student): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo $student['name']; ?></strong></td>
                                    <td><?php echo $student['email']; ?></td>
                                    <td><?php echo $student['course']; ?></td>
                                    <td>
                                        <span style="
                                            padding: 5px 12px; 
                                            border-radius: 20px; 
                                            font-weight: bold;
                                            background: <?php 
                                                echo match($student['grade']) {
                                                    'A' => '#10b981',
                                                    'B' => '#3b82f6',
                                                    'C' => '#f59e0b',
                                                    'D' => '#f97316',
                                                    'F' => '#ef4444',
                                                    default => '#6b7280'
                                                }; 
                                            ?>;
                                            color: white;
                                        ">
                                            <?php echo $student['grade']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="index.php?action=edit&id=<?php echo $student['id']; ?>" 
                                               class="btn btn-warning">✏️ Edit</a>
                                            <a href="index.php?action=delete&id=<?php echo $student['id']; ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this student?');">🗑️ Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
