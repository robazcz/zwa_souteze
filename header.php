<header>
        <nav class="navbar">
            <ul>
                <li><a href="home">Domov</a></li>
                <li class='login'><a href="<?php echo !strpos($_SERVER["REQUEST_URI"], "login")?"login?next=$_SERVER[REQUEST_URI]":"login"?>"><?php echo isset($_SESSION['username'])?$_SESSION['username']:"login"; ?></a></li>
            </ul>
        </nav>
    </header>