import _ from 'lodash';
import { ArrowLeftIcon, ArrowRightIcon } from 'lucide-react';
import { Button } from './ui/button';
import { cn } from '@/lib/utils';
import { usePagination } from '@/hooks/usePagination';

type PaginationProps = {
    numberOfPages: number;
    currentPage: number;
    onPageChange: (page: number) => void;
    onNextPage: (page: number) => void;
    onPrevPage: (page: number) => void;
};

const DotsMap: Record<string, string> = {
    'right-dots': '...',
    'left-dots': '...',
};

function Pagination({
    numberOfPages,
    currentPage = 1,
    onNextPage,
    onPrevPage,
    onPageChange,
}: PaginationProps) {
    const pages = usePagination({
        numberOfPages,
        currentPage: currentPage,
    });

    return (
        <ul className='flex gap-2'>
            <li>
                <Button
                    size='icon'
                    variant='ghost'
                    disabled={currentPage === 1}
                    onClick={() => onPrevPage(currentPage)}
                >
                    <ArrowLeftIcon />
                </Button>
            </li>
            {pages.map((page) => (
                <li key={page}>
                    <Button
                        className={cn({
                            'bg-arylide-yellow hover:bg-arylide-yellow/90':
                                currentPage === page,
                        })}
                        variant={currentPage === page ? 'default' : 'outline'}
                        onClick={() => {
                            if (
                                typeof page === 'string' &&
                                page === 'right-dots'
                            )
                                return onPageChange(numberOfPages);
                            if (
                                typeof page === 'string' &&
                                page === 'left-dots'
                            )
                                return onPageChange(1);
                            if (typeof page === 'number') onPageChange(page);
                        }}
                    >
                        {DotsMap[page] || page}
                    </Button>
                </li>
            ))}
            <li>
                <Button
                    size='icon'
                    variant='ghost'
                    disabled={currentPage === numberOfPages}
                    onClick={() => onNextPage(currentPage)}
                >
                    <ArrowRightIcon />
                </Button>
            </li>
        </ul>
    );
}

export default Pagination;
