
	<?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>
    <script>
        var actions = {};
    </script>

		<?php Database::closeConnection(); ?>
	</body>
</html>
