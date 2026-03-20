function setNewStatus(elem) {
  switch (elem.data("status")) {
    case "delist":
      document.location =
        "list/markAsNoLongerWanted/" + elem.data("listing_id");
      break;

    case "relist":
      document.location = "list/relist/" + elem.data("listing_id");
      break;
  }
}

function toggleNoShow(elem) {
  document.location =
    "list/toggleNoShow/" +
    elem.data("listing_id") +
    "/" +
    elem.data("request_id");
}

$(function () {
  $(".status-btn").click(function (e) {
    e.preventDefault();

    setNewStatus($(this));
  });
});

$(function () {
  $(".no-show-btn").click(function (e) {
    e.preventDefault();

    toggleNoShow($(this));
  });
});
