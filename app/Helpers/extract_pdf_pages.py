import sys
import os
import re
from pypdf import PdfReader

def extract_relevant_pages(pdf_path, keywords_str, max_pages=4):
    if not os.path.exists(pdf_path):
        return f"Error: File not found at {pdf_path}"
    
    try:
        reader = PdfReader(pdf_path)
        total_pages = len(reader.pages)
        
        if total_pages == 0:
            return "Error: PDF file is empty."
            
        # If the PDF is small, just extract everything
        if total_pages <= max_pages:
            content = []
            for i in range(total_pages):
                text = reader.pages[i].extract_text() or ""
                content.append(f"--- HALAMAN {i+1} ---\n{text.strip()}")
            return "\n\n".join(content)
            
        # Parse keywords
        keywords = [k.lower() for k in re.split(r'\s+', keywords_str) if len(k) > 2]
        
        page_scores = []
        for i in range(total_pages):
            text = reader.pages[i].extract_text() or ""
            text_lower = text.lower()
            score = 0
            # Score based on keyword frequency
            for kw in keywords:
                score += text_lower.count(kw)
            page_scores.append((i, score, text))
            
        # We always want to include page 0 (usually introduction/table of contents)
        selected_pages = {0}
        
        # Sort remaining pages by score descending
        sorted_by_score = sorted(page_scores[1:], key=lambda x: x[1], reverse=True)
        
        # Pick the top matches
        for i in range(min(max_pages - 1, len(sorted_by_score))):
            page_idx = sorted_by_score[i][0]
            selected_pages.add(page_idx)
            
        # Sort selected pages in chronological order
        selected_pages = sorted(list(selected_pages))
        
        content = []
        for idx in selected_pages:
            text = page_scores[idx][2]
            content.append(f"--- HALAMAN {idx+1} ---\n{text.strip()}")
            
        return "\n\n".join(content)
        
    except Exception as e:
        return f"Error reading PDF: {str(e)}"

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python extract_pdf_pages.py <pdf_path> [keywords]")
        sys.exit(1)
        
    pdf_path = sys.argv[1]
    keywords_str = sys.argv[2] if len(sys.argv) > 2 else ""
    
    result = extract_relevant_pages(pdf_path, keywords_str)
    print(result)
