        </div>
        <!-- END MAIN CONTENT WRAPPER -->
    </main>

    <!-- Global Scripts compartidos por módulos -->
    <script>
        // Utilidad global simple para fetch APIs
        async function apiCall(endpoint, method = 'GET', body = null) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const headers = { 'Content-Type': 'application/json' };
            if (csrfToken) headers['X-CSRF-Token'] = csrfToken;

            const options = { method, headers };
            if (body) options.body = JSON.stringify(body);

            const res = await fetch(endpoint, options);
            return res.json();
        }
    </script>
</body>
</html>
