jQuery(document).ready(function ($) {

  // List-Grid View Toggle
  $(document).on("click", ".bdlms-list-view, .bdlms-grid-view", function () {
    const isListView = $(this).hasClass("bdlms-list-view");
    
    $(".bdlms-course-list").toggleClass("list-view", isListView);
    $(".bdlms-list-view, .bdlms-grid-view").removeClass("active");
    $(this).addClass("active");
  });  

  // Lesson Sidebar Toggle
  $(".bdlms-lesson-toggle").on("click", function () {
    $(".bdlms-lesson-view").toggleClass("active");
  });

  // Sidebar - Course Content Toggle
  $(".bdlms-sidebar-toggle").on("click", function () {
    $(this).next(".bdlms-lesson-accordion").slideToggle();
  });
});

function openTab(evt, tabName) {
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


document.addEventListener("DOMContentLoaded", function () {
  // Get all tabs and their corresponding content
  const tablinks = document.querySelectorAll(".nav-link.goto-section");
  const tabcontents = document.querySelectorAll(".tab-pane");

  let firstAvailableTab = null;

  // Iterate through tabs to find the first tab with corresponding content
  tablinks.forEach((tab, index) => {
    const tabId = tab.getAttribute("aria-controls");
    const content = document.getElementById(tabId);

    if (content && content.innerHTML.trim() !== "" && !firstAvailableTab) {
      firstAvailableTab = { tab, content };
    }
  });

  // If a valid tab with content is found, activate it
  if (firstAvailableTab) {
    firstAvailableTab.tab.classList.add("active");
    firstAvailableTab.content.classList.add("active");
  }
});