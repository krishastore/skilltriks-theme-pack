function openTab(evt, tabName) {
  // Log tab name to confirm function is triggered
  console.log(`Opening tab: ${tabName}`);

  // Hide all tab panes
  const tabcontent = document.getElementsByClassName("tab-pane");
  for (let i = 0; i < tabcontent.length; i++) {
    tabcontent[i].classList.remove("active");
  }

  // Remove active class from all nav links
  const tablinks = document.getElementsByClassName("nav-link");
  for (let i = 0; i < tablinks.length; i++) {
    tablinks[i].classList.remove("active");
  }

  // Show current tab pane and mark as active
  document.getElementById(tabName).classList.add("active");
  evt.currentTarget.classList.add("active");
}
