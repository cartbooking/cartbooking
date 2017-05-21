function accordion(str) {
    var grow = document.getElementById(str);
    if (grow.clientHeight == "0") {
        var wrapper = document.getElementById("heightWrapper"+str);
        grow.style.height = wrapper.clientHeight + "px";
        grow.style.borderBottom = "1px solid #ededed";
    } else {
        grow.style.height = 0;
        grow.style.borderBottom = 0;
    }
};
function changeImage(str) {
    if (document.getElementById(str).src === "/images/close-02.svg") {
        if (document.getElementById(str).alt === "y") {
            document.getElementById(str).src = "/images/confirmed-02.svg";
        } else {
            document.getElementById(str).src = "/images/unconfirmed-02.svg";
        }
    } else {
        document.getElementById(str).src = "/images/close-02.svg";
    }
};
