<?php
    $id = $args['id'];
?>
<div class="jr-subbar" aria-label="Breadcrumb">
    <div class="container-xxl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb jr-breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="/">Top</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= get_the_title($id); ?> 
                </li>
            </ol>
        </nav>
    </div>
</div>