/**
 * Appends or inserts HTML content to an element with the specified ID.
 * 
 * @param {string} elementId - The ID of the target element.
 * @param {string} htmlContent - The HTML content to be inserted.
 * @param {string} position - Position to insert content ("before", "after", or "append").
 */
function appendSkeletonHTML(elementId, htmlContent, position = "append") {
    // Validate if the element ID is provided
    if (!elementId || typeof elementId !== "string") {
        console.error("Skeleton Helper - Invalid element ID provided.");
        return;
    }

    // Find the target element by ID
    const targetElement = document.getElementById(elementId);

    // Check if the element exists
    if (!targetElement) {
        console.error(`Skeleton Helper -  No element found with ID: ${elementId}`);
        return;
    }

    // Validate the position argument
    if (!["before", "after", "append"].includes(position)) {
        console.error(`Skeleton Helper - Invalid position specified. Use "before", "after", or "append".`);
        return;
    }

    // Insert or append the content based on the position
    switch (position) {
        case "before":
            targetElement.insertAdjacentHTML("beforebegin", htmlContent);
            break;
        case "after":
            targetElement.insertAdjacentHTML("afterend", htmlContent);
            break;
        case "append":
            targetElement.innerHTML += htmlContent;
            break;
        default:
            console.error("Skeleton Helper - Unexpected position value.");
    }
}

/**
 * Repeats the given HTML content a specified number of times and appends it to the element with the specified ID.
 *
 * @param {string} elementId - The ID of the target element.
 * @param {string} htmlContent - The HTML content to be repeated and appended.
 * @param {number} repeatCount - The number of times the content should be repeated.
 */
function repeatAndAppendSkeletonContent(elementId, htmlContent, repeatCount) {
    // Validate if the element ID is provided
    if (!elementId || typeof elementId !== "string") {
        console.error("Skeleton Helper - Invalid element ID provided.");
        return;
    }

    // Find the target element by ID
    const targetElement = document.getElementById(elementId);

    const targetTbody = targetElement.querySelector("tbody");


    // Check if the element exists
    if (!targetElement) {
        console.error(`Skeleton Helper - No element found with ID: ${elementId}`);
        return;
    }

    // Validate the repeat count
    if (!Number.isInteger(repeatCount) || repeatCount <= 0) {
        console.error("Skeleton Helper - Repeat count must be a positive integer.");
        return;
    }

    // Append the repeated content
    let repeatedContent = "";
    for (let i = 0; i < repeatCount; i++) {
        repeatedContent += htmlContent;
    }
    targetTbody.innerHTML = repeatedContent;
}