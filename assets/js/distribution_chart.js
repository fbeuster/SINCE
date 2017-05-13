var app = app || {};

app.distribution_chart = {
  arc     : null,
  chart   : '',
  color   : d3.scale.category20(),
  data    : null,
  height  : 256,
  margin  : 16,
  width   : 256,
  radius  : 0,

  dataAmount : function(d) {
    return d.amount;
  },

  dataTitle : function(d) {
    return d.data.c;
  },

  draw : function(chart, income_data) {
    this.chart  = chart;
    this.data   = income_data.slice();
    this.radius = Math.min(this.width, this.height) / 2;
    this.arc    = d3.svg.arc().outerRadius(this.radius);

    this.drawChart();
    this.drawLegend();
  },

  drawChart : function() {
    var that = this;
    var pie = d3.layout.pie()
                .value(this.dataAmount)
                .sort(null);

    var svg = d3.select(this.chart)
                .append("svg")
                  .attr("width", this.width)
                  .attr("height", this.height)
                  .append("g")
                    .attr("transform", "translate(" + this.width / 2 + "," + this.height / 2 + ")");

    var path = svg.datum(this.data)
                  .selectAll("path")
                  .data(pie)
                  .enter()
                  .append("path")
                    .attr("fill", this.fill)
                    .transition()
                    .duration(2000)
                    .attrTween('d', animate);

    path.each(function(d, i) {
      d3.select(this)
        .append('title')
          .text(that.dataTitle);
    });

    function animate(b) {
      b.innerRadius = 0;
      var i = d3.interpolate({startAngle: 0, endAngle: 0}, b);
      return function(t) { return that.arc(i(t)); };
    }
  },

  drawLegend : function() {
    var chart   = this.chart;
    var legend  = d3.select(this.chart)
                    .append("table");

    var tr      = legend.append("tbody")
                        .selectAll("tr")
                        .data(this.data)
                        .enter()
                        .append("tr");

    var td      = tr.append("td");

    var td_svg  = td.append("svg")
                      .attr("width", this.margin)
                      .attr("height", this.margin);

    td_svg.append("rect")
            .attr("width", this.margin)
            .attr("height", this.margin)
            .attr("fill", this.fill);

    tr.append("td")
        .text(function(d){ return d.c;});

    tr.append("td")
        .attr('class', 'number')
        .text(function(d){ return d.amount.toFixed(2) + ' €';});

    tr.append("td")
        .attr('class', 'number')
        .text(function(d){
          var total       = $(chart).attr('data-sum');
          var percentage  = d.amount * 100 / total;
          return percentage.toFixed(2) + ' %';
        });
  },

  fill : function(d, i) {
    if (d.data && d.data.color && d.data.color.length > 0) {
      return d.data.color;
    }

    if (d.color && d.color.length > 0) {
      return d.color;
    }

    return app.distribution_chart.color(i);
  }
}