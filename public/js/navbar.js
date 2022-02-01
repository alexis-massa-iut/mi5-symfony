function processForm() {
    // Go to URL/search
    search = document.getElementById("search-text").value;
    window.location.replace(`http://127.0.0.1:8000/shop/search/${search}`);
    return false;
}