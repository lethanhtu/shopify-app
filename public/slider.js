$(document).ready(function(){
  $('.your-class').slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    autoplay: false,
    dots: true,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          infinite: false,
          dots: true
        }
      }
    ]
  });
});