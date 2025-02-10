document.addEventListener("DOMContentLoaded", function () {

    const navToggle = document.querySelector(".nav-toggle");
    const nav = document.querySelector("nav");
    const divConteudo = document.querySelector(".content");
    navToggle.addEventListener("click", function () {
      nav.classList.toggle("nav-collapsed");
      if(nav.classList=="nav-collapsed"){     
        divConteudo.style.transform = 'translateX(-15%)';
        divConteudo.style.width = '100%';
        divConteudo.style.transition = '';
      }else{
        divConteudo.style.transition = 'transform 0.3s ease';
        divConteudo.style.transform = 'translateX(0%)';
        divConteudo.style.width = '85%';
      }
      
    });
  });

