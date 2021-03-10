let Elements;
window.onload = () => Elements = Array.from(document.getElementById("Index").children);

function Search(Value) {
    if (Value.length > 0) {
        Elements.forEach(Element => Element.classList.toggle("Hidden", !Element.textContent.includes(Value)));
    } else {
        Elements.forEach(Element => Element.classList.toggle("Hidden", false));
    }
}