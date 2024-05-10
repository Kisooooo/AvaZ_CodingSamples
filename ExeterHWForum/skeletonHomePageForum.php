<!--
This script combines backend server logic with front-end HTML and JavaScript to create the forum's front page,
that allows users to search posts by tags, upvote them, and log in or sign out.
-->

<?php    session_start();
    if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: skeletonHomePageForum.php"); // Redirect to the login/sign-up form if pressed logout
    exit();
}
?>


<?php
/*
    Database Connection and Data Retrieval:
    Establishes a connection to MySQL database run on a local computer.
    Executes a query to randomly fetch four posts from `poststoragetable` to display on the homepage.
    The data of these posts is stored in variables for later usage in the HTML section.
*/

    $servername = '127.0.0.1:3306';
    $username = 'root';
    $password = '';
    $dbname = 'poststorageschema';
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if($conn->connect_error){
        die('Connection failed');
    }


    function fetchPosts($conn, $limit = 4) {
        $sql = "SELECT * FROM poststoragetable ORDER BY RAND() LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    function generatePostHtml($postId, $title, $content, $upvotes) {
        return "
        <table position='absolute'>
            <tr>
                <td colspan='1'>
                    <center>
                        <button onclick='incrementVote($postId)'> Upvote </button>
                    </center> 
                </td>
                <td colspan='1'>
                    <input class='invis' type='number' id='upvoteNumber$postId' name='upvoteNumber$postId' value='$upvotes'><br>  
                </td>
                <td colspan='2' onclick=\"window.location.href='skeletonPostPage.php?id=$postId';\">
                    $title
                </td>
            </tr>
            <tr>
                <td colspan='4'>
                    <label for='postDescript$postId'>
                        Post Description:<br>
                    </label><br>
                    <input class='invis' type='text' id='postDescript$postId' name='postDescript$postId' value='$content'><br> 
                </td>
            </tr>
        </table>";
    }
    
    function displayPosts($conn) {
        $posts = fetchPosts($conn);
        while ($row = $posts->fetch_assoc()) {
            $postId = $row['id'];
            $title = $row['title'];
            $content = $row['content'];
            $upvotes = $row['score'];
            echo generatePostHtml($postId, $title, $content, $upvotes);
        }
    }
    
    $conn = connectDatabase();
    displayPosts($conn);
    $conn->close();
    ?>

                        
<html>
<!--
HTML section:
Main structure of the home page, set up using tables and providing space for post data, a search box, and login management.
Links external CSS for page styling and JavaScript functions for interactivity (both in separate files).
-->
    
    <head>
        <link rel="stylesheet" href="skeletonCSS.css">
        <script src="skeletonPageFunctions.js"></script>
    </head>

<body>
    <div class = "scrollable">
        <table position = "absolute">
            <tr> 
                <td width = "12%">1</td>
                <td width = "12%">2</td>
                <td width = "12%">3</td>
                <td width = "12%">4</td>
                <td width = "12%">5</td>
                <td width = "12%">6</td>
                <td width = "12%">7</td>
                <td width = "12%">8</td>
            </tr>
            <tr> 
                <td colspan = 2 id = "bold">logo</td>
                <td colspan = 3 id = "bold">
                    <body>
                        <!--from https://www.sliderrevolution.com/resources/css-search-box/-->
                        <table colspan = 4 border-collapse = "collapse">
                            <td colspan = 1>
                                <div class="dropdown">
                                    <button class="dropbtn">tagged search</button>
                                    <div class="dropdown-content" class = "scrollable-dropdown" min-width = "300px">
                                        <br>
                                        <button class = "tagSearch" id="anthroTag" onclick="addSearchTag('ant')">&nbsp;&nbsp;Anthropology&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="artTag" onclick="addSearchTag('art')">&nbsp;&nbsp;Art&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="bioTag" onclick="addSearchTag('bio')">&nbsp;&nbsp;Biology&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="chemTag" onclick="addSearchTag('che')">&nbsp;&nbsp;Chemistry&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="compsciTag" onclick="addSearchTag('csc')">&nbsp;&nbsp;Computer Science&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="econTag" onclick="addSearchTag('eco')">&nbsp;&nbsp;Economics&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="engTag" onclick="addSearchTag('eng')">&nbsp;&nbsp;English&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="greekTag" onclick="addSearchTag('grk')">&nbsp;&nbsp;Greek&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="healthTag" onclick="addSearchTag('hhd')">&nbsp;&nbsp;Health and Human Dev&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="historyTag" onclick="addSearchTag('his')">&nbsp;&nbsp;History&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="integTag" onclick="addSearchTag('int')">&nbsp;&nbsp;Integrated Studies&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="latinTag" onclick="addSearchTag('lat')">&nbsp;&nbsp;Latin&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="mathTag" onclick="addSearchTag('mat')">&nbsp;&nbsp;Mathematics&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="modlangTag" onclick="addSearchTag('modlang')">&nbsp;&nbsp;Modern Languages&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="musicTag" onclick="addSearchTag('mus')">&nbsp;&nbsp;Music&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="physicsTag" onclick="addSearchTag('phy')">&nbsp;&nbsp;Physics&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="psychTag" onclick="addSearchTag('psy')">&nbsp;&nbsp;Psychology&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="religionTag" onclick="addSearchTag('rel')">&nbsp;&nbsp;Religion&nbsp;&nbsp;</button><br><br>
                                        <button class = "tagSearch" id="theaterdanceTag" onclick="addSearchTag('thd')">&nbsp;&nbsp;Theater and Dance&nbsp;&nbsp;</button><br><br>
                                    </div>
                                </div>
                            </td>
                            <form action="search.php" method="post">
                            <td colspan = 3>
                                <label for="searchText">Search for posts here. Use single quotes for tags, commas to separate tags:</label><br><br><br>
                                <input type="text" id="searchText" name="searchText"><br><br>
                            </td>
                        </form>
                        </table>
                    
                                      
                </td>
                <td colspan = 1 id = "bold">
                    <div class="dropdown">
                        <button class="dropbtn">menu</button>
                        <div class="dropdown-content">
                            <a href="#">menu item 1</a>
                            <a href="#">menu item 2</a>
                            <a href="#">menu item 3</a>
                            <a href="#">menu item 4</a>
                        </div>
                    </div>
                </td>
                <td colspan="2" id="bold">
                    <div class="dropdown">
                        <?php
                        if (isset($_SESSION["username"])) { // User is logged in
                            $username = $_SESSION["username"];
                            $user_type = $_SESSION["user_type"];
                            echo '<button class="dropbtn">' . $username . '</button>';
                            echo '<span>User type: ' . $user_type . '</span>';
                            echo '<form method="post" action="">';
                            echo '<input type="submit" name="logout" value="Logout">';
                            echo '</form>';
                        } else {
                            // User is not logged in
                            echo '<button class="dropbtn">login</button>';
                            echo '<div class="dropdown-content-login">';
                            echo '<center>';
                            echo '<br>';
                            echo '<form id="loginForm" action="login.php" method="POST">';
                            echo '<label for="inputUsername">Username:</label><br>';
                            echo '<input type="text" placeholder="Enter Username" name="inputUsername" id="inputUsername" required><br><br>';
                            echo '<label for="inputPassword">Password:</label><br>';
                            echo '<input type="password" placeholder="Enter Password" name="inputPassword" id="inputPassword" required><br><br>';
                            echo '<button class="submittable" type="submit" onclick="submitLogin(event)">Login</button>';
                            echo '<label>';
                            echo '<input type="checkbox" checked="checked" name="remember"> Remember me';
                            echo '</label>';
                            echo '<div id="inputmsg">';
                            if (isset($errorMessage)) {
                                echo $errorMessage;
                            }
                            echo '</div>';
                            echo '</form>';
                            echo '<br>';
                            echo '<form id="signupForm" action="signup.php" method="POST">';
                            echo '<label for="signupUsername">New Username:</label><br>';
                            echo '<input type="text" placeholder="Enter New Username" name="signupUsername" id="signupUsername" required><br><br>';
                            echo '<label for="signupPassword">New Password:</label><br>';
                            echo '<input type="password" placeholder="Enter New Password" name="signupPassword" id="signupPassword" required><br><br>';
                            echo '<label for="signupUserType">User Type:</label><br>';
                            echo '<select id="signupUserType" name="signupUserType" required>';
                            echo '<option value="reg">Regular</option>';
                            echo '<option value="mod">Moderator</option>';
                            echo '</select><br><br>';
                            echo '<button class="submittable" type="submit" onclick="submitSignup(event)">Sign Up</button>';
                            echo '</form>';
                            echo '</center>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </td>


                <script>
                    /*
                    Function to handle login form submission.
                    Retrieves and stores entered username and password values in the browser's local storage.
                    Updates hidden fields with the entered data for form submission.
                     */

                    function submitLogin(event) {
                        event.preventDefault();
                        
                        // Retrieve the input data
                        var username = document.getElementById("inputUsername").value;
                        var password = document.getElementById("inputPassword").value;

                        localStorage.setItem("username", username);
                        localStorage.setItem("password", password);

                        document.getElementById("loginUsername").value = username;
                        document.getElementById("loginPassword").value = password;

                        document.getElementById("loginForm").submit();
                    }
                    
                    function handleLoginResponse(response) {
                        if (response.errorMessage) {
                            var errorDiv = document.getElementById("inputmsg");
                        if (response.errorMessage) {
                            // Display the error message in the dropdown ??? tbd
                            errorDiv.textContent = response.errorMessage;
                        } else {
                            window.location.href = "skeletonHomePageForum.php";
                        }
                    }
                    }

                    function submitSignup(event) {
                        event.preventDefault();
                        
                        // Retrieve the input data
                        var username = document.getElementById("signupUsername").value;
                        var password = document.getElementById("signupPassword").value;
                        var userType = document.getElementById("signupUserType").value;

                        localStorage.setItem("username", username);
                        localStorage.setItem("password", password);

                        document.getElementById("signupUsernameInput").value = username;
                        document.getElementById("signupPasswordInput").value = password;
                        document.getElementById("signupUserTypeInput").value = userType;

                        document.getElementById("signupForm").submit();
                    }
                </script>

        <!-- Forms to submit login and signup information to the server-->

                <form id="loginForm" action="login.php" method="POST">
                    <input type="hidden" name="inputUsername" id="loginUsername">
                    <input type="hidden" name="inputPassword" id="loginPassword">
                </form>

                <form id="signupForm" action="signup.php" method="POST">
                    <input type="hidden" name="signupUsername" id="signupUsernameInput">
                    <input type="hidden" name="signupPassword" id="signupPasswordInput">
                    <input type="hidden" name="signupUserType" id="signupUserTypeInput">
                </form>

             <?PHP

                // Setting up content on page
                function renderLeftBar() {
                    return '<td rowspan="2" colspan="2">left bar</td>';
                }
                
                function renderRightBar() {
                    return '<td rowspan="2" colspan="2">right bar</td>';
                }
                
                function renderSortDropdown() {
                    return '<td colspan="2">
                        <div class="dropdown">
                            <button class="dropbtn">sort</button>
                            <div class="dropdown-content">
                                <a href="#">new</a>
                                <a href="#">popular</a>
                            </div>
                        </div>
                    </td>';
                }
            
            // Link to another script handling create post page
                function renderCreateNewPostButton() {
                    return '<td colspan="2" onclick="window.location.href=\'skeletonCreatePostPageRevised.html\';">
                        create a new post (click to link to page)
                    </td>';
                }
                
                function renderPost($postId, $title, $content, $upvotes, $tags) {
                    $tagsHtml = implode('', array_map(fn($tag) => "<td><button onclick=\"window.location.href='skeletonTagsPage.html';\"> $tag </button></td>", $tags));
                    return "
                    <table position='absolute'>
                        <tr>
                            <td colspan='1'>
                                <center>
                                    <button onclick='incrementVote($postId)'> Upvote </button>
                                </center> 
                            </td>
                            <td colspan='1'>
                                <input class='invis' type='number' id='upvoteNumber$postId' name='upvoteNumber$postId' value='$upvotes'><br>  
                            </td>
                            <td colspan='2' onclick=\"window.location.href='skeletonPostPage.php?id=$postId';\">
                                $title
                            </td>
                        </tr>
                        <tr>
                            <td colspan='4'>
                                <label for='postDescript$postId'>
                                    Post Description:<br>
                                </label><br>
                                <input class='invis' type='text' id='postDescript$postId' name='postDescript$postId' value='$content'><br> 
                            </td>
                        </tr>
                        <tr>
                            <td colspan='4'>
                                <div>
                                    <table class='forumPostTable' class='scrollable'>
                                        <tr max-height='30px'>
                                            $tagsHtml
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>";
                }
                
                echo '<table>';
                echo '<tr>' . renderLeftBar() . renderSortDropdown() . renderCreateNewPostButton() . renderRightBar() . '</tr>';
                echo '<tr><td rowspan="1" colspan="4"><table class="fitting">';
                echo renderPost(0, 'Sample Title 0', 'Sample content for post 0', 5, ['Tag 1', 'Tag 2', 'Tag 3']); // FOr testing, to be filled in
                echo renderPost(1, 'Sample Title 1', 'Sample content for post 1', 10, ['Tag A', 'Tag B', 'Tag C']);
                echo '</table></td></tr>';
                echo '</table>';
            
                
    </body>
</html>
