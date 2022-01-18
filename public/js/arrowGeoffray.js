const arrow = document.querySelector("#arrow");
const element_to_change = document.querySelectorAll(".tricksContainer");
let arrayOfChange = [];

function isInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

element_to_change.forEach(element => {
    document.addEventListener("scroll",()=>{
        arrayOfChange = [];
        arrayOfChange.push(isInViewport(element));
    });
});

document.addEventListener("scroll",()=>{
    if(arrayOfChange.every(v=>v===true)){
        console.log(arrayOfChange.includes(true));
        arrow.classList.add("return0");
        arrow.classList.remove("return180");
    }else{
        arrow.classList.add("return180");
        arrow.classList.remove("return0");
    }
});