var app = app || {};

app.income_chart = {
  arc     : null,
  color   : d3.scale.category20(),
  data    : null,
  height  : 320,
  margin  : 16,
  width   : 320,
  radius  : 0,

  animate : function(b) {
    b.innerRadius = 0;
    var i = d3.interpolate({startAngle: 0, endAngle: 0}, b);
    return function(t) { return app.income_chart.arc(i(t)); };
  },

  dataAmount : function(d) {
    return d.amount;
  },

  dataTitle : function(d) {
    return d.data.c;
  },

  draw : function(income_data) {
    this.data   = income_data.slice();
    this.radius = Math.min(this.width, this.height) / 2;
    this.arc    = d3.svg.arc().outerRadius(this.radius);

    this.drawChart();
    this.drawLegend();
  },

  drawChart : function() {
    var pie = d3.layout.pie()
                .value(this.dataAmount)
                .sort(null);

    var svg = d3.select("#income_chart")
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
                    .attrTween('d', this.animate);

    path.each(function(d, i) {
      d3.select(this)
        .append('title')
          .text(app.income_chart.dataTitle);
    });
  },

  drawLegend : function() {
    var legend  = d3.select('#income_chart')
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
        .text(function(d){ return d.amount + ' €';});
  },

  fill : function(d, i) {
    return app.income_chart.color(i);
  }
}