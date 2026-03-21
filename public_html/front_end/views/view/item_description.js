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
  $.ajax({
    url:
      "list/toggleNoShow/" +
      elem.data("listing_id") +
      "/" +
      elem.data("request_id"),
  }).done(function () {
    if (elem.text().trim() === "Mark as no show") {
      elem.text("Remove no show");
      elem.removeClass("btn-danger").addClass("btn-success");
    } else {
      elem.text("Mark as no show");
      elem.removeClass("btn-success").addClass("btn-danger");
    }

    var userId = elem.data("user_id");

    $.ajax({
      url: "list/getReliabilityScore/" + userId,
    }).done(function (reliabilityScore) {
      var newScore = parseInt(reliabilityScore);
      var newClass =
        newScore >= 8
          ? "badge-success"
          : newScore >= 5
            ? "badge-warning"
            : "badge-danger";

      var badge = $(".reliability-score-badge-" + userId);
      badge.text("Reliability Score: " + newScore + "/10");
      badge
        .removeClass("badge-success badge-warning badge-danger")
        .addClass(newClass);
    });
  });
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
