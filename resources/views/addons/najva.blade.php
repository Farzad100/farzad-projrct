<script type="text/javascript">
    (function () {
        var now = new Date();
        var version = now.getFullYear().toString() + "0" + now.getMonth() + "0" + now.getDate() +
            "0" + now.getHours();

        var head = document.getElementsByTagName("head")[0];

        var link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = "https://app.najva.com/static/css/local-messaging.css" + "?v=" + version;
        head.appendChild(link);

        var script = document.createElement("script");
        script.type = "text/javascript";
        script.async = true;
        script.src = "https://app.najva.com/static/js/scripts/ghesta-website-9671-8750faa1-33a8-41d1-bfe3-99cf91406fab.js" + "?v=" + version;
        head.appendChild(script);
    })()
</script>
<link rel="manifest" href="/manifest.json">
