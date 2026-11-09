// JavaScript source code
//window.addEventListener('load', function () {
//    const fileInput = document.getElementById('edtAddTikect');
//    const file = new File(["contenu du fichier"], "exemple.txt", { type: "text/plain" });

//    const dataTransfer = new DataTransfer();
//    dataTransfer.items.add(file);
//    fileInput.files = dataTransfer.files;

//    // Déclencher l'événement change
//    const event = new Event('change');
//    fileInput.dispatchEvent(event);
//});
window.addEventListener('load', function () {
    const fileInput = document.getElementById('edtAddTikect');
    const file = new File(["contenu du fichier"], "exemple.txt", { type: "text/plain" });

    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    fileInput.files = dataTransfer.files;

    // Déclencher l'événement change
    const event = new Event('change');
    fileInput.dispatchEvent(event);

    // Accéder au fichier chargé
    fileInput.addEventListener('change', function () {
        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            console.log(e.target.result); // Contenu du fichier
        };

        reader.readAsText(file);
    });
});
//const inputElement = document.getElementById("edtAddTikect");
//inputElement.addEventListener("change", handleFiles, false);
//function handleFiles() {
//    const fileList = this.files; /* now you can work with the file list */
//}