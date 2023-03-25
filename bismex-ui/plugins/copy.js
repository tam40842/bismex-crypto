export default ({
    app,
}, inject) => {
    function copy(v, msg) {
        if (navigator.clipboard && window.isSecureContext) {
            // navigator clipboard api method'
            navigator.clipboard.writeText(v)
                .then(() => {
                    app.$toast.success(msg || 'Copy Successed!');
                })
                .catch((error) => {
                    app.$toast.error('Copy Error!');
                });
        } else {
            // text area method
            let textArea = document.createElement("textarea");
            textArea.value = v;
            // make the textarea out of viewport
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy') ? app.$toast.success(msg || 'Copy Successed!') : app.$toast.error('Copy Error!');;
            textArea.remove();
        }
    }
    inject('copy', copy)
    app.$copy = copy
}