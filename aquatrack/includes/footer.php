    </main>
  </div>
</div>
<div class="modal fade" id="helpModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="eva eva-info-outline"></i> About <?= APP_NAME ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body small">
        <p><?= APP_NAME ?> — <?= SYSTEM_SUBTITLE ?>.</p>
        <p class="mb-1">This system was made as a final requirement for the subject: <strong>CC105 – Information Management</strong>.</p>
        <ul class="mb-2">
          <li>Instructor: <strong>Sir Samuel S. Mindaro, Jr.</strong></li>
          <li>Front-end: <strong>Mark Joaqui G. Pedro</strong></li>
          <li>Back-end: <strong>Kian Lorenz D. Galario</strong></li>
        </ul>
        <p class="mb-0">Use the left panel to navigate. Report issues to the system admin.</p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/app.js"></script>
</body>
</html>