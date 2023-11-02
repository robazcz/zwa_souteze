<header>
        <nav class="navbar">
            <ul>
                <li><a href="/">Domov</a></li>
                <li class='login'><a href="/login.php"><?php echo isset($_SESSION['username'])?$_SESSION['username']:"login"; ?></a></li>
            </ul>
        </nav>
    </header>