import { Input, InputProps } from '../ui/input';
import { SearchIcon } from 'lucide-react';

type SearchbarProps = InputProps;
function Searchbar(props: SearchbarProps) {
    return (
        <div className='relative flex items-center text-black/20'>
            <SearchIcon className='absolute left-2' />
            <Input
                placeholder='Search...'
                className='rounded-lg pl-10 text-xs text-black placeholder:text-black/20 focus-visible:ring-transparent'
                {...props}
            />
        </div>
    );
}

export default Searchbar;
