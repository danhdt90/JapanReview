<footer id="jr-footer" class="jr-footer py-6 py-lg-8" aria-labelledby="footer-title">
    <div class="container-xxl text-center">

        <!-- Logo -->
        <h2 id="footer-title" class="jr-footer-logo">
            <a href="/" class="d-inline-block">
                <img src="<?= IMAGE_PATH; ?>/logo.svg" alt="Japan Review Logo" width="150">
            </a>
        </h2>

        <!-- Menu -->
        <?php 
            $footer = brandwoods_render_menu('menu_footer');
            if ( $footer !== false && count($footer) > 0) :
                ?>
                    <ul class="jr-footer-nav mb-4">
                        <?php 
                            foreach ($footer as $index => $items) :
                                ?>
                                    <li><a href="<?= $items->url; ?>"><?= $items->title; ?></a></li>
                                <?php
                            endforeach;
                        ?>
                    </ul>
                <?php
            endif;
        ?>

        <!-- Note -->
        <div class="jr-footer-note small mx-auto">
            <p class="mb-0">
                This official website is administered and managed by the International Research Center for Japanese Studies (Nichibunken).
                The URLs and contents of this official site are subject to change without notice.<br>
                Nichibunken is not responsible for the content of external sites to which links are provided on this site.<br>
                © International Research Center for Japanese Studies All rights reserved
            </p>
        </div>

    </div>

    <!-- Back to Top -->
    <button type="button" id="btn-top" class="btn-top" aria-label="Back to top">
        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
            <path d="M0 19L9.5 0L19 19H0Z" fill="#1A5E89" />
        </svg>
    </button>
</footer>

<?php include('includes/js-footer.php'); ?>

</body>

</html>