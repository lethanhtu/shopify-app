$(document).ready(function(){
  $('.recommendation-items').slick({
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