      <footer class="main-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <p><img width="15" height="15" src="../img/<?php echo $sf_row['logo'];?>" /> <?php echo $schoolName; ?> &middot; <?php echo date("l"); ?>, <?php echo date("M".". "."d".", "."Y"); ?></p>
            </div>
            <div class="col-sm-6 text-right">
              <p>Design by <a href="https://bootstrapious.com" class="external">Bootstrapious</a></p>
            </div>
          </div>
        </div>
      </footer>
      
      <!-- Global Toast Container -->
      <div aria-live="polite" aria-atomic="true" style="position: relative; z-index: 9999;">
        <div style="position: fixed; top: 20px; right: 20px;" id="toast-container">
        </div>
      </div>
      
      <script>
      function showToast(title, message, type = 'success') {
          var toastId = 'toast-' + Date.now();
          var bgClass = type === 'success' ? 'bg-success text-white' : (type === 'error' ? 'bg-danger text-white' : 'bg-info text-white');
          var toastHtml = `
          <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
            <div class="toast-header ${bgClass}">
              <strong class="mr-auto">${title}</strong>
              <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close" style="color: white; opacity: 1; outline: none; border: none; background: transparent; float: right;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="toast-body" style="background: white; color: #333;">
              ${message}
            </div>
          </div>`;
          
          // Use setTimeout to ensure jQuery is loaded if this is called early
          setTimeout(function() {
              if (window.jQuery) {
                  $('#toast-container').append(toastHtml);
                  $('#' + toastId).toast('show');
                  $('#' + toastId).on('hidden.bs.toast', function () {
                      $(this).remove();
                  });
              }
          }, 100);
      }
      
      // Auto-show toast based on URL parameters
      document.addEventListener("DOMContentLoaded", function() {
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.has('successMsg')) {
              showToast('Success', urlParams.get('successMsg'), 'success');
              // Clean up URL
              window.history.replaceState({}, document.title, window.location.pathname);
          } else if (urlParams.has('errorMsg')) {
              showToast('Error', urlParams.get('errorMsg'), 'error');
              window.history.replaceState({}, document.title, window.location.pathname);
          }
      });
      </script>
      
      <?php
      $sf_query=null;
      $conn=null;
      ?>