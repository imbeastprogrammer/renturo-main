export const useSearchParams = () => {
    const searchParams = new URLSearchParams(window.location.search);
    const queryParams = Object.fromEntries(searchParams.entries());

    return { searchParams, queryParams };
};
