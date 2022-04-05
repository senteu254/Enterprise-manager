<script src="../js/jquery-1.9.1.js"></script>
  <script src="../js/Chart.min.js"></script>
  <script src="js/pretty-doughtnut.js"></script>
<style>
body {
  margin: 0;
} 
canvas {
  z-index: 100;
}

.margin {
  margin-top: 5px;
  margin-bottom: 1px;
  margin-left: 20px;
  margin-right: 20px;
  }
  
.labelPercentage {
  font-size: x-large;
}
 
.labelText {
  font-size: small;
  color: #666666;
}
 
.labelContainer {
  display: block;
  text-align: center;
  width: 100px;
  font-family: Helvetica;
}
</style>
<script>
$(window).load(function() {
  doughnutWidget.options = {
    container: $('#container'),
    width: 100,
    height: 100,
    class: 'myClass',
    cutout: 50
  };

  doughnutWidget.render(data());

  setInterval(init, 2000);
});

function init() {
  doughnutWidget.render(data());
}

function data() {
    var data = {
    pending: {
      val: Math.round(Math.random() * 100),
      color: '#57B4F2',
      click: function(e) {
        console.log('hi');
      }
    },
    delivered: {
      val: Math.round(Math.random() * 100),
      color: '#6DED5C'
    },
    delayed: {
      val: Math.round(Math.random() * 100),
      color: '#E63329',
      link: 'http://www.google.com'
    },
	 deleted: {
      val: Math.round(Math.random() * 100),
      color: '#E63329',
      link: 'http://www.google.com'
    }
  };

  return data;
}
</script>
