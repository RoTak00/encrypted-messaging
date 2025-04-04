(function (w, i, r, e, b, oar, d) {
  if (!w[b]) {
    w.WireBoardNamespace = w.WireBoardNamespace || [];
    w.WireBoardNamespace.push(b);
    w[b] = function () {
      (w[b].q = w[b].q || []).push(arguments);
    };
    w[b].q = w[b].q || [];
    oar = i.createElement(r);
    d = i.getElementsByTagName(r)[0];
    oar.async = 1;
    oar.src = e;
    d.parentNode.insertBefore(oar, d);
  }
})(
  window,
  document,
  "script",
  "https://static.wireboard.io/wireboard.js",
  "wireboard"
);
wireboard("newTracker", "wb", "pipeline-0.collector.wireboard.io", {
  appId: "bROUwBcP",
  forceSecureTracker: true,
  contexts: {
    performanceTiming: true,
  },
});
window.wireboard("enableActivityTracking", 5, 10);
var customContext = [
  {
    schema:
      "iglu:com.snowplowanalytics.snowplow/custom_context/jsonschema/1-0-0",
    data: { publisher: "ef7b3f90-5fe5-4af1-9fb1-2ce81bcbe3e3" },
  },
];
window.wireboard("trackPageView", null, customContext);
