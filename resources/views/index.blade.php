<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/html">

    <head>
        <title>Laravel</title>

        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <script src="/bower_components/angular/angular.js"></script>
        <script src="/bower_components/angular-resource/angular-resource.js"></script>

        <!--ngTags-input - obligatoriamente tras angular-->
        <script type="application/javascript" src="bower_components/ng-tags-input/ng-tags-input.js"></script>
        <link rel="stylesheet" href="bower_components/ng-tags-input/ng-tags-input.min.css">  

        <script src="/bower_components/jquery/dist/jquery.js"></script>
        <script src="/bower_components/bootstrap/dist/js/bootstrap.js"></script>
        <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.css" type="text/css">
        <link rel="stylesheet" href="bower_components/bootstrap-social/bootstrap-social.css" type="text/css">
        <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.css" type="text/css">

        <script type="application/javascript" src="/js/logout.js"></script>
        <script type="application/javascript" src="/js/tag.js"></script>
        <link rel="stylesheet" href="/css/index.css">

        <!--<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
        
        <script>var rootApp = angular.module('rootApp', ['logout','usertags'])</script>
        
        <style>

            .plot {
                font: 10px sans-serif;
            }

            .axis path,
            .axis line {
                fill: none;
                stroke: #000;
                shape-rendering: crispEdges;
            }

            .x.axis path {
                display: none;
            }

            .line {
                fill: none;
                stroke: steelblue;
                stroke-width: 1.5px;
            }

        </style>
        
    </head>

    <body ng-app="rootApp">

    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container" ng-app="logout" ng-controller="LogoutController">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img alt="Brand" src="img/saveet.png"></a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                    <li><a href="#world"><span class="glyphicon glyphicon-globe"></span> World trends</a></li>
                    <li><a href="#location"><span class="glyphicon glyphicon-tags"></span> Location trends</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user"></span> {{ Auth::user()->name }}
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                            <li><a href="#"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#" ng-click="logout()"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container">

        <div class="starter-template">

            <div class="content" ng-app="usertags" ng-controller="HashtagController" >

                    <h1>Your personal hashtags:</h1>
                    <tags-input ng-model="tags" on-tag-added="envia ( $tag.text )" on-tag-removed="elimina ( $tag.text )" >
                        <auto-complete source="loadTags($query)"></auto-complete>
                    </tags-input>

                    <div class="loading" ng-show="loading">
                        <i class="fa fa-circle-o-notch fa-4x fa-spin"></i>
                    </div>

            </div>
        </div>
    </div><!-- /.container -->

    <div class="plot" style="text-align: center">
        <script>

            var margin = {top: 20, right: 80, bottom: 30, left: 50},
                    width = 960 - margin.left - margin.right,
                    height = 500 - margin.top - margin.bottom;

            var parseDate = d3.time.format("%Y-%m-%dT%H:%M:%S.%LZ").parse;

            var x = d3.time.scale()
                    .range([0, width]);

            var y = d3.scale.linear()
                    .range([height, 0]);

            var color = d3.scale.category10();

            var xAxis = d3.svg.axis()
                    .scale(x)
                    .orient("bottom");

            var yAxis = d3.svg.axis()
                    .scale(y)
                    .orient("left");

            var line = d3.svg.line()
                    .interpolate("basis")
                    .x(function(d) { return x(d.date); })
                    .y(function(d) { return y(d.temperature); });

            var svg = d3.select(".plot").append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            d3.json("v1/es/test", function(error, data) {
                if (error) throw error;

                color.domain(d3.keys(data[0]).filter(function(key) { return key !== "date"; }));

                data.forEach(function(d) {
                    d.date = parseDate(d.key_as_string);
                });

                var cities = color.domain().map(function(name) {
                    return {
                        name: name,
                        values: data.map(function(d) {
                            return {date: d.date, temperature: d.doc_count};
                        })
                    };
                });

                x.domain(d3.extent(data, function(d) { return d.date; }));

                y.domain([
                    d3.min(cities, function(c) { return d3.min(c.values, function(v) { return v.temperature; }); }),
                    d3.max(cities, function(c) { return d3.max(c.values, function(v) { return v.temperature; }); })
                ]);

                svg.append("g")
                        .attr("class", "x axis")
                        .attr("transform", "translate(0," + height + ")")
                        .call(xAxis);

                svg.append("g")
                        .attr("class", "y axis")
                        .call(yAxis)
                        .append("text")
                        .attr("transform", "rotate(-90)")
                        .attr("y", 6)
                        .attr("dy", ".71em")
                        .style("text-anchor", "end")
                        .text("Temperature (ºF)");

                var city = svg.selectAll(".city")
                        .data(cities)
                        .enter().append("g")
                        .attr("class", "city");

                city.append("path")
                        .attr("class", "line")
                        .attr("d", function(d) { return line(d.values); })
                        .style("stroke", function(d) { return color(d.name); });

                city.append("text")
                        .datum(function(d) { return {name: d.name, value: d.values[d.values.length - 1]}; })
                        .attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.temperature) + ")"; })
                        .attr("x", 3)
                        .attr("dy", ".35em")
                        .text(function(d) { return d.name; });
            });

        </script>

    </div>
    <footer class="footer">
        <div class="git-logo">
            <p class="text-muted">Check this project on GitHub
                <a class="btn btn-social-icon btn-github" href="https://github.com/jlightyear/bootcampinc" target="_blank">
                    <i class="fa fa-github"></i>
                </a>
            </p>
        </div>

    </footer>
    </body>
</html>

