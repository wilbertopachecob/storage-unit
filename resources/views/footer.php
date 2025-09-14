<!-- Footer -->
<footer class="footer font-small">
    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">© <?=date("Y");?> Copyright: Engr. Wilberto Pacheco Batista
    </div>
    <!-- Copyright -->

  </footer>
  <!-- Footer -->

<!-- jQuery and Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
// Load Popper.js with fallback chain
(function() {
    const popperSources = [
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js',
        'https://unpkg.com/popper.js@1.14.6/dist/umd/popper.min.js',
        'js/popper.min.js'
    ];
    
    function loadPopper(index = 0) {
        if (index >= popperSources.length) {
            console.error('All Popper.js sources failed to load');
            return;
        }
        
        const script = document.createElement('script');
        script.src = popperSources[index];
        script.crossOrigin = 'anonymous';
        script.onload = function() {
            console.log('Popper.js loaded from:', popperSources[index]);
        };
        script.onerror = function() {
            console.warn('Failed to load Popper.js from:', popperSources[index]);
            loadPopper(index + 1);
        };
        document.head.appendChild(script);
    }
    
    loadPopper();
})();
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

</body>
</html>