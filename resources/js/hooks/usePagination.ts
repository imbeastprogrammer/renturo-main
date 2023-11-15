import _ from 'lodash';
import { useMemo } from 'react';

type UsePaginationParams = {
    numberOfPages: number;
    siblingCount?: number;
    currentPage: number;
};

export const usePagination = ({
    numberOfPages,
    siblingCount = 1,
    currentPage,
}: UsePaginationParams) => {
    const paginationRange = useMemo(() => {
        const totalPageNumbers = siblingCount + 5;

        if (totalPageNumbers >= numberOfPages) {
            return _.range(1, numberOfPages + 1);
        }

        const leftSiblingIndex = Math.max(currentPage - siblingCount, 1);
        const rightSiblingIndex = Math.min(
            currentPage + siblingCount,
            numberOfPages,
        );

        const shouldShowLeftDots = leftSiblingIndex > 2;
        const shouldShowRightDots = rightSiblingIndex < numberOfPages - 2;

        const firstPageIndex = 1;
        const lastPageIndex = numberOfPages;

        if (!shouldShowLeftDots && shouldShowRightDots) {
            let leftItemCount = 3 + 2 * siblingCount;
            let leftRange = _.range(1, leftItemCount + 1);

            return [...leftRange, 'right-dots', numberOfPages];
        }
        if (shouldShowLeftDots && !shouldShowRightDots) {
            let rightItemCount = 3 + 2 * siblingCount;
            let rightRange = _.range(
                numberOfPages - rightItemCount + 1,
                numberOfPages + 1,
            );
            return [firstPageIndex, 'left-dots', ...rightRange];
        }
        if (shouldShowLeftDots && shouldShowRightDots) {
            let middleRange = _.range(leftSiblingIndex, rightSiblingIndex + 1);
            return [
                firstPageIndex,
                'left-dots',
                ...middleRange,
                'right-dots',
                lastPageIndex,
            ];
        }

        return [];
    }, [numberOfPages, siblingCount, currentPage]);

    return paginationRange;
};
