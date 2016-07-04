/*
Author: Simeng Yang
Project: ICS4U Final Project - Database-Driven Website with PHP & MySQL
Item: JavaScript for website.HTML menu
Desc: Expand and collapse behaviors of menu on mouse hover/removal
*/

function expand(s)
{
  var td = s;
  var d = td.getElementsByTagName("div").item(0);

  td.className = "menuHover";
  d.className = "menuHover";
}

function collapse(s)
{
  var td = s;
  var d = td.getElementsByTagName("div").item(0);

  td.className = "menuNormal";
  d.className = "menuNormal";
}