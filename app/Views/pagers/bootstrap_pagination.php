<?php $pager->setSurroundCount(2) ?>

<?php if ($pager->hasPrevious() || $pager->hasNext() || count($pager->links()) > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <!-- Tombol Previous -->
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" class="page-link" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </span>
            </li>
        <?php endif ?>

        <!-- Link Halaman -->
        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <?php if ($link['uri']): ?>
                    <a href="<?= $link['uri'] ?>" class="page-link"><?= $link['title'] ?></a>
                <?php else: ?>
                    <span class="page-link"><?= $link['title'] ?></span>
                <?php endif ?>
            </li>
        <?php endforeach ?>

        <!-- Tombol Next -->
        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" class="page-link" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link" aria-label="Next">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </li>
        <?php endif ?>
    </ul>
</nav>
<?php endif; ?>