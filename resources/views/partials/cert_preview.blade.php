<div class="modal fade" id="certModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preview Sertifikat</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">

        <!-- PDF jadi gambar -->
        <canvas id="certCanvas" style="max-width:100%;display:none"></canvas>

        <!-- Image biasa -->
        <img id="certImg" style="max-width:100%;display:none" />

      </div>
    </div>
  </div>
</div>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<script>
  document.addEventListener('click', function (e) {
    var t = e.target.closest('.preview-cert');
    if (!t) return;

    e.preventDefault();

    var src = t.dataset.src;
    var type = (t.dataset.type || '').toLowerCase();

    // tampilkan modal
    $('#certModal').modal('show');

    var canvas = document.getElementById('certCanvas');
    var img = document.getElementById('certImg');

    // reset tampilan
    canvas.style.display = 'none';
    img.style.display = 'none';

    if (type === 'pdf') {
      var loadingTask = pdfjsLib.getDocument(src);

      loadingTask.promise.then(function (pdf) {
        pdf.getPage(1).then(function (page) {

          var viewport = page.getViewport({ scale: 1.5 });
          var context = canvas.getContext('2d');

          canvas.height = viewport.height;
          canvas.width = viewport.width;

          page.render({
            canvasContext: context,
            viewport: viewport
          });

          canvas.style.display = 'block';
        });
      });

    } else {
      img.src = src;
      img.style.display = 'block';
    }
  });
</script>