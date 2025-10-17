// Collection of helpful javascript functions

/**
 * Runs the function when the document is ready
 * @param fn function to run
 */
export default function documentReady(fn) {
    document.addEventListener('DOMContentLoaded', fn(), false)
}