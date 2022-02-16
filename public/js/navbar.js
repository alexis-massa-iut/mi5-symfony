function processForm(url) {
    // Go to URL/search
    search = document.getElementById("search-text").value;
    window.location.replace(url + '/' + search);
    return false;
}