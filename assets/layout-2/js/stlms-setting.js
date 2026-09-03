jQuery(function($) {
  // List-Grid View Toggle
  $(document).on("click", ".stlms-list-view, .stlms-grid-view", function () {
    const isListView = $(this).hasClass("stlms-list-view");
    $(".stlms-course-list").toggleClass("list-view", isListView);
    $(".stlms-list-view, .stlms-grid-view").removeClass("active");
    $(this).addClass("active");
  });  
  // Lesson Sidebar Toggle
  $(".stlms-lesson-toggle").on("click", function () {
    $(".stlms-lesson-view").toggleClass("active");
  });
  // Sidebar - Course Content Toggle
  $(".stlms-sidebar-toggle").on("click", function () {
    $(this).next(".stlms-lesson-accordion").slideToggle();
  });
});

window.openTab = function (evt, tabName) {
	// Hide all tab panes
	const tabContent = document.getElementsByClassName('tab-pane');
	for (const tab of tabContent) {
		tab.classList.remove('active');
	}

	// Remove active class from all nav links
	const tabLinks = document.getElementsByClassName('nav-link');
	for (const link of tabLinks) {
		link.classList.remove('active');
	}

	// Show current tab pane and mark link as active
	const currentTab = document.getElementById(tabName);
	if (currentTab) {
		currentTab.classList.add('active');
	}
	evt.currentTarget.classList.add('active');
};

document.addEventListener("DOMContentLoaded", function () {
  // Get all tabs and their corresponding content
  const tablinks = document.querySelectorAll(".nav-link");
  let firstAvailableTab = null;
  // Iterate through tabs to find the first tab with corresponding content
  tablinks.forEach((tab) => {
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