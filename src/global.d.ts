// global.d.ts — ambient type declarations for the gallery

interface GalleryData {
  topics: TopicData[];
}

interface TopicData {
  name: string;
  tags: TagData[];
}

interface TagData {
  name: string;
  images: ImageItem[];
}

interface ImageItem {
  filename: string;
  thumbnail: string;
  full: string | null;
  topic: string;
  tag: string;
  created: number | null;
}

interface Window {
  __GALLERY_DATA__: GalleryData;
}
