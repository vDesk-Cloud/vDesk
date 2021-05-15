let Elements;
window.onload = () => Elements = Array.of(
    ...Array.from(document.getElementById("Classes").children),
    ...Array.from(document.getElementById("Interfaces").children),
    ...Array.from(document.getElementById("Traits").children)
);

function Search(Value) {
    window.requestAnimationFrame(
        () => {
            if(Value.length > 0) {
                Elements.forEach(Element => Element.classList.toggle("Hidden", !Element.textContent.includes(Value)));
            } else {
                Elements.forEach(Element => Element.classList.toggle("Hidden", false));
            }
        }
    );
}